<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SupportTicketReply;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'author_role' => ['required', 'string', Rule::in(SupportTicketReply::ROLES)],
            'author_name' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
