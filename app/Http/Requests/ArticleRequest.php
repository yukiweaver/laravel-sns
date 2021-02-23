<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u', // スペース、「/」はタグで使用禁止
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'tags' => 'タグ',
        ];
    }

    /**
     * json形式のtagsリクエストから不要なデータを削除したcollectionを返す
     * passedValidationはバリデーションが成功した後に自動的に呼ばれる
     */
    public function passedValidation()
    {
        // $this->tagsは以下の形でリクエスト
        // "[{"text":"USA","tiClasses":["ti-valid"]},{"text":"France","tiClasses":["ti-valid"]}]"
        $this->tags = collect(json_decode($this->tags))
            ->slice(0, 5) // タグは最大5個まで登録可能
            ->map(function($requestTag) {
                return $requestTag->text;
            });
    }
}
