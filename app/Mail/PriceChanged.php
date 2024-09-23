<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PriceChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $price;

    public function __construct($price)
    {
        $this->price = $price;
    }

    public function build()
    {
        return $this->view('emails.price_changed')
                    ->with(['price' => $this->price]);
    }
}
