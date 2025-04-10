<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

final class UpdateCartQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the cart item from the route
        /** @var CartItem|null $cartItem */
        $cartItem = $this->route('cartItem');

        // Check if cart item exists and belongs to the authenticated user's active cart
        return $cartItem instanceof CartItem 
            && $cartItem->cart 
            && $cartItem->cart->user_id === Auth::id() 
            && !$cartItem->cart->is_completed; // Can only update items in active cart
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var CartItem|null $cartItem */
        $cartItem = $this->route('cartItem');

        // Basic rules if cart item isn't loaded (authorize should prevent this usually)
        if (!$cartItem instanceof CartItem || !$cartItem->product) {
             return [
                 'quantity' => ['required', 'integer', 'min:1'],
             ];
         }
        
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $cartItem->product->stock],
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
