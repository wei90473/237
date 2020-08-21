<?php

namespace App\Http\Requests\WebBookPlace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WebBookPlaceRequest extends FormRequest
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
            'applydate'=>'required',
            'reason'=>'required',
            'num'=>'integer',
            'orgname'=>'required|alpha_dash',
            'applyuser'=>'required|alpha_dash',
            'tel'=>'required',
            'email'=>'required|email',
            'passwd'=>'required',
        ];
    }

    public function messages()
    {
        return [
            //'receiptname.required' => '收據抬頭必填',
            'applydate.required' => '申請日期必填',
            'reason.required'=>'活動名稱(事由)必填',
            'reason.alpha_dash'=>'活動名稱(事由)不可包含特殊符號',
            'num.integer'=>'人數只能填數字',
            'orgname.required'=>'申請單位(服務機關)必填',
            'orgname.alpha_dash'=>'申請單位(服務機關)不可包含特殊符號',
            'applyuser.required'=>'聯絡人(申請人)必填',
            'applyuser.alpha_dash'=>'聯絡人(申請人)不可包含特殊符號',
            'tel.required'=>'連絡電話必填',
            'tel.alpha_dash'=>'連絡電話不可包含特殊符號',
            'email.required'=>'電子信箱必填',
            'email.email'=>'電子信箱 必須符合信箱格式',
            'passwd.required'=>'修改密碼必填',
        ];
    }
}
