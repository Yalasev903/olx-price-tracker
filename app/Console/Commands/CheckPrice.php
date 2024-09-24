<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\Http;
use App\Mail\PriceChanged;

class CheckPrice extends Command
{
    protected $signature = 'check:price';
    protected $description = 'Check prices for subscriptions';

    public function handle()
    {
        // Получаем все подписки, которые подтверждены
        $subscriptions = Subscription::where('is_verified', 1)->get();

        foreach ($subscriptions as $subscription) {
            // Логика парсинга цены
            $currentPrice = $this->getPriceFromUrl($subscription->url);

            // Проверяем, изменилась ли цена
            if ($currentPrice !== null && $currentPrice != $subscription->last_checked_price) {
                // Сохраняем историю цены
                PriceHistory::create([
                    'subscription_id' => $subscription->id,
                    'price' => $currentPrice,
                    'checked_at' => now(),
                ]);

                // Отправляем уведомление пользователю
                $this->notifyUser($subscription->email, $currentPrice);

                // Обновляем последнюю проверенную цену
                $subscription->last_checked_price = $currentPrice;
                $subscription->save();
            }
        }
    }

    public function getPriceFromUrl($url)
    {
        try {
            // Отправляем GET-запрос
            $response = Http::get($url);
    
            // Проверяем успешность запроса
            if ($response->successful()) {
                $html = $response->body();
    
                // Логируем полученный HTML
                \Log::info("Fetched HTML for URL {$url}: {$html}");
    
                // Логика парсинга HTML для получения цены
                preg_match('/<h3 class="css-90xrc0">([\d\s]+ грн\.)<\/h3>/', $html, $matches);
    
                // Если цена найдена, возвращаем её
                if (isset($matches[1])) {
                    return (float) str_replace([' ', 'грн.'], '', $matches[1]); // Убираем пробелы и символ валюты
                }
            }
        } catch (\Exception $e) {
            // Логируем ошибки
            \Log::error("Error fetching price from URL: {$url}", ['error' => $e->getMessage()]);
        }
    
        return null; // Если произошла ошибка, возвращаем null
    }
    

    private function notifyUser($email, $price)
    {
        // Отправляем email с уведомлением о смене цены
        \Mail::to($email)->send(new PriceChanged($price));
    }
}
