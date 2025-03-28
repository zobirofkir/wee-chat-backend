<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => "required|string|max:255",
            'email' => "required|string|email|max:255|unique:users",
            'username' => "required|string|max:255|unique:users",
            'avatar' => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            'phone' => "nullable|string|max:255",
            'password' => "required|string|min:8|confirmed",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'avatar.image' => 'Avatar must be an image',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file',
            'avatar.max' => 'Avatar size must not exceed 2MB',
            'phone.max' => 'Phone number must not exceed 255 characters',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'username.max' => 'Username must not exceed 255 characters',
            'name.max' => 'Name must not exceed 255 characters',
            'password.min' => 'Password must be at least 8 characters',
            'password.max' => 'Password must not exceed 255 characters',
        ];
    }
}
