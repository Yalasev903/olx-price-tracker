<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;

        // Создаем URL для подтверждения подписки
        $this->verificationUrl = route('subscription.verify', [
            'id' => $this->subscription->id,
            'token' => $this->subscription->verification_token,
        ]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.verify')
            ->subject('Please verify your subscription')
            ->with(['verificationUrl' => $this->verificationUrl]);
    }
}
