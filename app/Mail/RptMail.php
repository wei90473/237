<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //{{$data['yerly']}}年度{{$data['class_name_term']}}


        $data=array(
            "yerly"=>"01080",
            "class_name_term"=>"測試班期",
            "sate_edate"=>"01080",
            "quota"=>"01080",
            "site"=>"01080",
            "branch_name"=>"01080",
            "user_name"=>"01080",
            "tel"=>"01080",
            "email"=>"01080"
        );


        // return $this->view('view.name');
        return $this->view('/email/H3')
        // ->attachRaw($this->pdf, 'name.pdf', [
        //     'mime' => 'application/pdf',
        // ])
        ->subject('testmail')
        ->with([
            'data' => $data,
        ]);
    }
}
