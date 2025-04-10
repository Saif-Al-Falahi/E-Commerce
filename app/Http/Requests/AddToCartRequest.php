<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

final class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming any authenticated user can add to cart
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Retrieve the product from the route
        /** @var Product|null $product */
        $product = $this->route('product');

        // Ensure product exists and has stock information
        if (!$product instanceof Product || !isset($product->stock)) {
             // You might want to throw a specific exception or handle this differently
             // For now, let validation handle the case where product might be missing
             return [
                 'quantity' => ['required', 'integer', 'min:1'],
             ];
         }

        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->stock],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Please specify a quantity.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'The requested quantity exceeds available stock.',
        ];
    }
}
