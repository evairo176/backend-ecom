<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public function __construct($token)
    {
        $this->data = $token;
    }


    public function build()
    {
        $data = $this->data;
        return $this->from('support@evairo.co.id')
        ->view('mail.forget',compact('data'))
        ->subject('Password reset link');
    }
}
