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
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|integer|in:1,2',
        ];
    }

    public function messages()
    {
        return [
            'item_id.required' => 'item_idを含めてください',
            'item_id.exists' => '指定されたitem_idは存在しません',

            'address_id.required' => 'address_idを含めてください',
            'address_id.exists' => '指定されたaddress_idは存在しません',

            'payment_method.required' => 'payment_methodを含めてください',
            'payment_method.integer' => 'payment_methodは整数で入力してください',
            'payment_method.in' => 'payment_methodは1または2を指定してください',
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
