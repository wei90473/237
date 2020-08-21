<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class H3 extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $data;
    protected $file;

    public function __construct($_data,$_file)
    {
        $this->data=$_data;
        $this->file=$_file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

                return $this->view('/email/H3')
                ->subject('講座聘請通知')
                ->with([
                    'data' => $this->data
                ])
                ->attach($this->file, [
                    'as' => '教學方法調查表.docx',
                    'mime' => 'application/vnd.ms-word',
                ]);
        
    }
}
