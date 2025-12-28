<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
        $categoryType = $this->input('category_type', 'main');
        
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'category_type' => ['required', 'in:main,sub'],
            'parent_id' => [
                'required_if:category_type,sub',
                'nullable',
                'exists:categories,id',
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
    
    protected function prepareForValidation(): void
    {
        // If category_type is main, ensure parent_id is null
        if ($this->input('category_type') === 'main') {
            $this->merge(['parent_id' => null]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفئة مطلوب',
            'name.unique' => 'اسم الفئة مستخدم بالفعل',
            'category_type.required' => 'نوع الفئة مطلوب',
            'category_type.in' => 'نوع الفئة غير صحيح',
            'parent_id.required_if' => 'يجب اختيار الفئة الرئيسية عند إنشاء فئة فرعية',
            'parent_id.exists' => 'الفئة الأصل المحددة غير موجودة',
        ];
    }
}
