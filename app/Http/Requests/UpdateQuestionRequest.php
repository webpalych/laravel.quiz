<?php

namespace App\Http\Requests;

class UpdateQuestionRequest extends Request
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
            'question_text' => 'required | unique:questions,question_text,'.$this->questions,
            'language_id' => 'required | exists:languages,id',
            'answers' => 'array | between:4,4',
            'answers.*.id' => 'sometimes | numeric',
            'answers.*.answer_text' => 'required',
            'answers.*.is_right' => 'required | boolean',
        ];
    }
}
