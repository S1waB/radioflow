<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RadioAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $radioName;
    public $role;

    public function __construct($name, $email, $password, $radioName, $role)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->radioName = $radioName;
        $this->role = $role;
    }

    public function build()
    {
        return $this->subject('Your RadioFlow Account Has Been Created')
                    ->markdown('emails.radio-account-created');
    }
}