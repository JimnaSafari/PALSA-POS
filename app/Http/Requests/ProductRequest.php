<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->input('productID');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId)
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'purchaseprice' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
                'lt:price' // Purchase price should be less than selling price
            ],
            'categoryName' => [
                'required',
                'exists:categories,id'
            ],
            'count' => [
                'required',
                'integer',
                'min:0',
                'max:10000'
            ],
            'description' => [
                'required',
                'string',
                'max:1000'
            ],
            'image' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($productId)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists.',
            'price.min' => 'Price must be greater than 0.',
            'purchaseprice.lt' => 'Purchase price must be less than selling price.',
            'image.dimensions' => 'Image must be between 100x100 and 2000x2000 pixels.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Clean and format input data
        if ($this->has('price')) {
            $this->merge([
                'price' => (float) str_replace(',', '', $this->price)
            ]);
        }
        
        if ($this->has('purchaseprice')) {
            $this->merge([
                'purchaseprice' => (float) str_replace(',', '', $this->purchaseprice)
            ]);
        }
    }
}