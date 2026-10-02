<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'kind' => ['required', Rule::in(['personal', 'incentive'])],
            'company_id' => ['nullable', 'integer', 'min:1'],
            'owner_key' => ['nullable', 'string', 'max:80'],
        ];
    }
}
