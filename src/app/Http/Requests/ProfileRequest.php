<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'post' => 'required|string|regex:/^\d{3}-\d{4}$/|max:255',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '画像を選択してください。',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'name.required' => 'お名前を入力してください。',
            'post.required' => '郵便番号を入力してください',
            'post.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
        ];
    }
}
