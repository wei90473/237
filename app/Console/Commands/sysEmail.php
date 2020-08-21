<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Term_processService;
use Illuminate\Support\Facades\Mail;

class sysEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sys_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '班務流程通知';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct(Term_processService $term_processService)
    {
        $this->term_processService = $term_processService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mail_data = $this->term_processService->getMail();
        foreach($mail_data as $mail_row){
            $data = array(
                'title' => $mail_row['title'],
                'content' => $mail_row['content'],
            );
            $mail = $mail_row['mail_to'];
            Mail::send("email/send", $data, function ($message) use ($mail,$data){
                $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
                $message->subject($data['title']);
                $message->to($mail);
            });
        }
    }
}
