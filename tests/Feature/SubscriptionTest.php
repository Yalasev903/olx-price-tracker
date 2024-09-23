<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase; // Очищает базу данных перед каждым тестом

    protected function setUp(): void
    {
        parent::setUp();
        // Удаляем все подписки перед каждым тестом безопасно
        Subscription::query()->delete();
    }

    public function test_successful_subscription()
    {
        Mail::fake(); // Заглушка для отправки email

        $response = $this->postJson('/subscribe', [
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Subscription successful. Please check your email to verify.'
        ]);

        Mail::assertSent(VerificationEmail::class, function ($mail) {
            return $mail->hasTo('user@example.com');
        });

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'user@example.com',
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
            'is_verified' => false
        ]);
    }

    public function test_duplicate_subscription()
    {
        // Создаем первую подписку в базе
        Subscription::create([
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
            'email' => 'user@example.com',
            'verification_token' => Str::random(32),
            'is_verified' => false,
        ]);

        // Пытаемся снова подписаться с теми же данными
        $response = $this->postJson('/subscribe', [
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(409)->assertJson([
            'message' => 'You are already subscribed to this URL.'
        ]);

        $this->assertCount(1, Subscription::where('email', 'user@example.com')->where('url', 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html')->get());
    }
}
