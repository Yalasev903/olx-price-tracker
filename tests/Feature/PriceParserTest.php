<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http; // Не забудьте импортировать класс Http
use App\Mail\PriceChanged;

class PriceParserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_parse_price_from_url()
    {
        // Мокируем ответ HTTP
        Http::fake([
            'example.com/test-ad' => Http::response('<span class="price">150.00</span>'),
        ]);

        $url = 'http://example.com/test-ad';
        $expectedPrice = 150.00; // Ожидаемая цена

        // Логика для получения и парсинга цены
        $price = (new \App\Console\Commands\CheckPrice)->getPriceFromUrl($url);

        $this->assertEquals($expectedPrice, $price);
    }

    /** @test */
    public function it_sends_notification_when_price_changes()
    {
        Mail::fake();

        $subscription = Subscription::factory()->create([
            'url' => 'http://example.com/test-ad',
            'email' => 'user@example.com',
            'last_checked_price' => 100.00,
            'is_verified' => 1,
        ]);

        // Мокируем ответ HTTP с новой ценой
        Http::fake([
            'example.com/test-ad' => Http::response('<span class="price">150.00</span>'),
        ]);

        // Выполняем команду проверки цен
        $command = new \App\Console\Commands\CheckPrice;
        $command->handle();

        // Проверяем, что уведомление было отправлено
        Mail::assertSent(PriceChanged::class, function ($mail) use ($subscription) {
            return $mail->hasTo($subscription->email) && $mail->price == 150.00;
        });

        // Проверяем, что цена обновилась в подписке
        $subscription->refresh();
        $this->assertEquals(150.00, $subscription->last_checked_price);
    }
}
