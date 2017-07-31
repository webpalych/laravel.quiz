<?php
/**
 * Created by PhpStorm.
 * User: Palych
 * Date: 31.07.2017
 * Time: 10:02
 */

namespace App\Http\Requests;


class UpdateQuizRequest extends Request
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
            'quiz_name' => 'required',
        ];
    }
}