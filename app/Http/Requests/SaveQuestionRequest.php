<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SaveQuestionRequest extends Request
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
            'question_text' => 'required | unique:questions',
            'answers' => 'array | between:4,4',
            'answers.*.answer_text' => 'required'
        ];
    }
}
