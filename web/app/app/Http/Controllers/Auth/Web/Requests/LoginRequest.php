<?php
namespace App\Http\Controllers\Auth\Web\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ];
    }

    /**
     * customize msg error
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return redirect()->back()
            ->withErrors($validator)
            ->with([
                'editModal' => 'editModal',
                'msg'       => $this->input()
            ]);
    }
}
