<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesProductCountries;
use App\Support\Currencies;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    use NormalizesProductCountries;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareCountriesForValidation();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'code' => [
                'sometimes',
                'string',
                'max:80',
                Rule::unique('products', 'code')
                    ->ignore($id)
                    ->where('company_id', $this->input('company_id')),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'technical_sheet' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', Rule::in(Currencies::CODES)],
            'is_active' => ['sometimes', 'boolean'],
            ...$this->countryRules(),
        ];
    }
}
