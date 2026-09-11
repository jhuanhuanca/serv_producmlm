<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Models\Document;
use Illuminate\Validation\Rule;

trait NormalizesDocumentPayload
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('url') && ! $this->filled('file_path')) {
            $this->merge(['file_path' => $this->input('url')]);
        }
    }

    /** @return array<string, mixed> */
    protected function documentRules(bool $partial): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'title' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_path' => [$required, 'string', 'max:2048'],
            'file_type' => [$required, 'string', Rule::in(Document::TYPES)],
            'original_name' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
