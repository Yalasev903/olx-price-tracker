<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use App\Mail\PriceChanged;

class CheckPrice extends Command
{
    protected $signature = 'check:price';
    protected $description = 'Check prices for subscriptions';

    public function handle()
    {
        $subscriptions = Subscription::where('is_verified', 1)->get();
        foreach ($subscriptions as $subscription) {
            // Логика парсинга цены
            $currentPrice = $this->getPriceFromUrl($subscription->url);

            if ($currentPrice !== null && $currentPrice != $subscription->last_checked_price) {
                // Логика отправки уведомления
                $this->notifyUser($subscription->email, $currentPrice);
                $subscription->last_checked_price = $currentPrice;
                $subscription->save();
            }
        }
    }

    private function getPriceFromUrl($url)
    {
        // Реализуйте логику парсинга цены из URL
        // Верните цену как число
    }

    private function notifyUser($email, $price)
    {
        \Mail::to($email)->send(new PriceChanged($price));
    }
}
