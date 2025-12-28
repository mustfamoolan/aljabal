<?php

namespace App\Http\Requests\Representatives;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWithdrawalSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'min_withdrawal_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'min_withdrawal_amount.required' => 'الحد الأدنى للسحب مطلوب',
            'min_withdrawal_amount.numeric' => 'الحد الأدنى يجب أن يكون رقماً',
            'min_withdrawal_amount.min' => 'الحد الأدنى يجب أن يكون على الأقل 0',
        ];
    }
}
