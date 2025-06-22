<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'text' => 'required|max:400',
            'image' => 'image|mimes:jpeg,png'
        ];
    }

    public function messages()
    {
        return [
            'text.required' => '本文を入力してください',
            'text.max' => '本文は400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpg」形式でアップロードしてください'
        ];
    }
}
