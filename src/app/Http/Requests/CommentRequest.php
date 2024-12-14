<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CommentRequest extends FormRequest
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
            'comment' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'item_id.required' => 'item_idを含めてください',
            'item_id.exists' => '指定されたitem_idは存在しません',
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは255文字以下で入力してください',
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
