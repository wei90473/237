<?php
namespace App\Services;

use Mail;
use Lang;
use Auth;
use App\Models\Setting;
use Config;


class EmailService
{

    static function send($data)
    {
        // 畫面預覽
        // return view('/email/contact', compact('data'));
        $managerEmail = Setting::where('unit','manager_email')->first();

        Mail::send('/email/contact', ['data' => $data], function($mail) use($data, $managerEmail)
        {
            $mail->from(config('mail.username'), Config::get('app.email_service_form'));

            $mail->to($managerEmail->value, $data['name'])->subject(Config::get('app.email_service_subject'));
        });
    }
}
