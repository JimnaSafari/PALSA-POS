<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[\d\s\-\+\(\)]+$/|max:20',
            'paymentMethod' => 'required|exists:payments,id',
            'paySlipImage' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
            'orderCode' => 'required|string|max:50',
            'totalAmount' => 'required|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'items.*.price' => 'required|numeric|min:0.01'
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number.',
            'paySlipImage.max' => 'Payment slip image must not exceed 5MB.',
            'items.required' => 'At least one item is required for the order.',
            'items.*.quantity.max' => 'Maximum quantity per item is 100.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^\d\+\-\(\)\s]/', '', $this->phone)
            ]);
        }
    }
}