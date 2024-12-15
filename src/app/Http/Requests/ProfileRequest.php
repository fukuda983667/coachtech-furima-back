<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProfileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'image_path' => 'nullable|file|mimes:jpeg,png|max:1024',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名前を入力してください',
            'name.string' => '名前は文字列で入力してください',
            'name.max' => '名前は255文字以下で入力してください',

            'image_path.file' => '画像はファイル形式でアップロードしてください',
            'image_path.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'image_path.max' => '画像は1MB以下にしてください',

            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はXXX-XXXXの形式で入力してください',

            'address.required' => '住所を入力してください',
            'address.string' => '住所は文字列で入力してください',
            'address.max' => '住所は255文字以下で入力してください',

            'building_name.required' => '建物名を入力してください',
            'building_name.string' => '建物名は文字列で入力してください',
            'building_name.max' => '建物名は255文字以下で入力してください',
        ];
    }

    // バリデーションに失敗した際のレスポンス
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'message' => '入力に誤りがあります',
            'errors' => $validator->errors()
        ], 422));
    }
}
