<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png|max:10240',
            'name' => 'required|string|max:255',
            'categories' => 'required',
            'condition_id' => 'required',
            'description' => 'required|string|max:255',
            'price' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '画像を選択してください。',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'image.max' => '画像が大きすぎます。',
            'name.required' => 'お名前を入力してください。',
            'categories.required' => 'カテゴリーを選択してください。',
            'condition_id.required' => '状態を選択してください。',
            'description.required' => '説明を入力してください。',
            'price.required' => '価格を入力してください。',
        ];
    }
}
