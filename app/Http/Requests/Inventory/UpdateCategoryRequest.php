<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')?->id ?? $this->category;
        $categoryType = $this->input('category_type', $this->route('category')?->parent_id ? 'sub' : 'main');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($categoryId)],
            'category_type' => ['sometimes', 'required', 'in:main,sub'],
            'parent_id' => [
                'required_if:category_type,sub',
                'sometimes',
                'nullable',
                'exists:categories,id',
            ],
            'description' => ['sometimes', 'nullable', 'string'],
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
