<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'email' => 'required|email|exists:rrt_users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'phone' => 'required|string|max:15',
            'tax_type' => 'required|in:1,2',
            'payment_method' => 'required|in:paypal,bank',
        ];

        if ($this->payment_method === 'bank') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:255',
                'bank_owner' => 'required|string|max:255',
                'bank_number' => 'required|string|max:50',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.exists' => 'The provided email does not exist in our records.',
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'dob.required' => 'The date of birth field is required.',
            'dob.date' => 'The date of birth must be a valid date.',
            'phone.required' => 'The phone number field is required.',
            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 15 characters.',
            'tax_type.required' => 'The tax type field is required.',
            'tax_type.in' => 'The selected tax type is invalid.',
            'payment_method.required' => 'The payment method field is required.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'bank_name.required' => 'The bank name field is required.',
            'bank_name.string' => 'The bank name must be a string.',
            'bank_name.max' => 'The bank name may not be greater than 255 characters.',
            'bank_owner.required' => 'The bank owner field is required.',
            'bank_owner.string' => 'The bank owner must be a string.',
            'bank_owner.max' => 'The bank owner may not be greater than 255 characters.',
            'bank_number.required' => 'The bank number field is required.',
            'bank_number.string' => 'The bank number must be a string.',
            'bank_number.max' => 'The bank number may not be greater than 50 characters.',
        ];
    }
}
