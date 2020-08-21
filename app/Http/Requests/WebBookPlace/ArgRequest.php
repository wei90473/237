<?php

namespace App\Http\Requests\WebBookPlace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArgRequest extends FormRequest
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
            //'receiptname' => 'required',
            'name'=>'required',
            'param1'=>'required|integer',
        ];
    }

    public function messages()
    {
        return [
            //'receiptname.required' => '收據抬頭必填',
            'name.required' => '折扣說明必填',
            'param1.required'=>'折扣數必填',
        ];
    }
}
