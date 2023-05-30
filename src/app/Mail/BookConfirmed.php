<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookConfirmed extends Mailable
{
    /** @var $fromEmail */
    private $fromEmail;

    private $information;

    use Queueable, SerializesModels;

    /**
     * Registration constructor.
     * @param $fromEmail
     * @param $mobile
     * @param $password
     */
    public function __construct($fromEmail, $information)
    {
        $this->fromEmail = $fromEmail;
        $this->information = $information;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromName = 'CHITTAGONG PORT AUTHORITY';
        $subject = 'Booking confirmed!';

        return $this->from(
            [$this->fromEmail, $fromName]
        )->subject($subject)
            ->view('watchman.emails.booking_confirmed')
            ->with(['information' => $this->information]);
    }
}
