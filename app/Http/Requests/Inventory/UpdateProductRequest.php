<?php

namespace App\Http\Requests\Inventory;

use App\Enums\SizeType;
use App\Enums\UnitType;
use App\Enums\WeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id ?? $this->product;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => ['sometimes', 'nullable', 'string', Rule::unique('products', 'sku')->ignore($productId)],
            'is_original' => ['sometimes', 'nullable', 'boolean'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'subcategory_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'supplier_id' => ['sometimes', 'nullable', 'exists:suppliers,id'],
            'author' => ['sometimes', 'nullable', 'string', 'max:255'],
            'publisher' => ['sometimes', 'nullable', 'string', 'max:255'],
            'purchase_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'retail_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'min_quantity' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'unit_type' => ['sometimes', 'nullable', Rule::enum(UnitType::class)],
            'weight_unit' => ['sometimes', 'nullable', Rule::enum(WeightUnit::class)],
            'weight_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'size' => ['sometimes', 'nullable', Rule::enum(SizeType::class)],
            'page_count' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'is_hardcover' => ['sometimes', 'nullable', 'boolean'],
            'carton_quantity' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'set_quantity' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'shelf' => ['sometimes', 'nullable', 'string', 'max:50'],
            'compartment' => ['sometimes', 'nullable', 'string', 'max:50'],
            'short_description' => ['sometimes', 'nullable', 'string'],
            'long_description' => ['sometimes', 'nullable', 'string'],
            'video_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'images' => ['sometimes', 'nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048'],
            'tags' => ['sometimes', 'nullable', 'array'],
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
            'sku.unique' => 'كود المنتج مستخدم بالفعل',
            'images.max' => 'يمكن رفع 10 صور كحد أقصى',
            'images.*.image' => 'يجب أن تكون الملفات صور',
            'images.*.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }
}
