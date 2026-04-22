<?php

namespace App\Http\Requests\Inventory;

use App\Enums\SizeType;
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

    protected function prepareForValidation()
    {
        if ($this->has('parts') && is_string($this->parts)) {
            $decoded = json_decode($this->parts, true);
            if (is_array($decoded)) {
                $this->merge([
                    'parts' => $decoded
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isOriginal = filter_var($this->input('is_original', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->where(function ($query) use ($isOriginal) {
                    return $query->where('is_original', $isOriginal);
                })
            ],
            'sku' => ['nullable', 'string', 'unique:products,sku'],
            'is_original' => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'translator' => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'retail_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'unit_type' => ['nullable', Rule::enum(UnitType::class)],
            'weight_unit' => ['nullable', Rule::enum(WeightUnit::class)],
            'weight_value' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', Rule::enum(SizeType::class)],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'is_hardcover' => ['nullable', 'boolean'],
            'carton_quantity' => ['nullable', 'integer', 'min:1'],
            'set_quantity' => ['nullable', 'integer', 'min:1'],
            'shelf' => ['nullable', 'string', 'max:50'],
            'compartment' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'parts' => ['nullable', 'array'],
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
            'name.unique' => 'هذا المنتج موجود مسبقاً في النظام',
            'sku.unique' => 'كود المنتج مستخدم بالفعل',
            'images.*.image' => 'يجب أن تكون الملفات صور',
            'images.*.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }
}
