<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;


class CheckPriceJob implements ShouldQueue
{
    use Queueable;

    private static $previousPrices = [];

    public function handle(): void
    {
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            try {
                // Получаем текущую цену
                $currentPrice = $this->fetchPriceFromOLX($subscription->url);

                // Проверяем, изменилась ли цена
                if ($this->priceHasChanged($subscription->id, $currentPrice)) {
                    $this->sendPriceChangeEmail($subscription->email, $currentPrice);
                }
            } catch (\Exception $e) {
                Log::error('Ошибка при обработке подписки: ' . $e->getMessage());
            }
        }
    }

    // Метод для проверки, изменилась ли цена
    private function priceHasChanged($subscriptionId, $newPrice)
    {
        $previousPrice = self::$previousPrices[$subscriptionId] ?? null;

        if ($previousPrice !== $newPrice) {
            // Обновляем предыдущую цену
            self::$previousPrices[$subscriptionId] = $newPrice;
            return true;
        }

        return false;
    }
}




