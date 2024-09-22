<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $verificationUrl = route('subscription.verify', [
            'id' => $this->subscription->id,
            'token' => $this->subscription->verification_token,
        ]);

        return $this->view('emails.verify')
            ->subject('Please verify your subscription')
            ->with(['verificationUrl' => $verificationUrl]);
    }
}
