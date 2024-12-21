<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ItemRequest extends FormRequest
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

    // バリデーション前のデータ準備
    public function prepareForValidation()
    {
        // 画像送信のためにフロントからmultipart/form-data形式でリクエスト送信される。
        // この時、配列を送信しても文字列に変換されてしまうため、リクエストデータが文字列ならJson配列に変換している。
        if ($this->has('categories') && !is_array($this->input('categories'))) {
            // 文字列を配列にデコードしてリクエストにマージ
            $categories = json_decode($this->input('categories'), true);
            $this->merge(['categories' => $categories]);
        }
    }

    public function rules(): array
    {
        return [
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id', // 各IDがcategoriesテーブルに存在することを確認
            'condition_id' => 'required|exists:item_conditions,id',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:1|max:9999999',
            'image_path' => 'required|file|mimes:jpeg,png|max:1024', // JPEG/PNG形式のみ許可、1MBまで
        ];
    }

    public function messages()
    {
        return [
        'categories.required' => 'カテゴリーを選択してください',
        'categories.array' => 'カテゴリーは配列形式で選択してください',
        'categories.*.exists' => '選択したカテゴリーは無効です',

        'condition_id.required' => '商品状態を選択してください',
        'condition_id.exists' => '選択した商品状態は無効です',

        'name.required' => '商品名を入力してください',
        'name.string' => '商品名は文字列で入力してください',
        'name.max' => '商品名は255文字以下で入力してください',

        'brand.required' => 'ブランド名を入力してください',
        'brand.string' => 'ブランド名は文字列で入力してください',
        'brand.max' => 'ブランド名は255文字以下で入力してください',

        'description.required' => '商品説明を入力してください',
        'description.string' => '商品説明は文字列で入力してください',
        'description.max' => '商品説明は255文字以下で入力してください',

        'price.required' => '価格を入力してください',
        'price.integer' => '価格は整数で入力してください',
        'price.min' => '価格は1以上で入力してください',
        'price.max' => '価格は9999999以下で入力してください',

        'image_path.required' => '画像をアップロードしてください',
        'image_path.file' => '画像はファイル形式でアップロードしてください',
        'image_path.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
        'image_path.max' => '画像は1MB以下でアップロードしてください',
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
