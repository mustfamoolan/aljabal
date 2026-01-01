<?php

namespace App\Http\Requests\Inventory;

use App\Enums\ProductType;
use App\Enums\UnitType;
use App\Enums\WeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'unique:products,sku'],
            'product_type' => ['required', Rule::enum(ProductType::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'retail_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'unit_type' => ['nullable', Rule::enum(UnitType::class)],
            'weight_unit' => ['nullable', Rule::enum(WeightUnit::class)],
            'weight_value' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'string', 'max:100'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'carton_quantity' => ['nullable', 'integer', 'min:1'],
            'set_quantity' => ['nullable', 'integer', 'min:1'],
            'shelf' => ['nullable', 'string', 'max:50'],
            'compartment' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:100'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
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
            'name.required' => 'اسم المنتج مطلوب',
            'product_type.required' => 'نوع المنتج مطلوب',
            'sku.unique' => 'كود المنتج مستخدم بالفعل',
            'images.max' => 'يمكن رفع 10 صور كحد أقصى',
            'images.*.image' => 'يجب أن تكون الملفات صور',
            'images.*.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }
}
