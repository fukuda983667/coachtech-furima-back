<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class PurchaseRequest extends FormRequest
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
            'item_id' => 'required|exists:items,id',
            'payment_method' => 'required|integer|in:1,2',
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'item_id.required' => 'item_idを含めてください',
            'item_id.exists' => '指定されたitem_idは存在しません',
            'payment_method.required' => 'payment_methodを含めてください',
            'payment_method.integer' => 'payment_methodは整数で入力してください',
            'payment_method.in' => 'payment_methodは1または2を指定してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はXXX-XXXXの形式で入力してください',
            'address.required' => '住所を入力してください',
            'address.max' => '住所は255文字以下で入力してください',
            'building_name.required' => '建物名を入力してください',
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
