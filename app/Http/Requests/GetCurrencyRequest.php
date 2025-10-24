<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'string', 'size:3'],
            'to' => ['required', 'string', 'size:3', 'different:from'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }
}
