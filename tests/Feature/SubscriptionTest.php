<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class SubscriptionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example()
{
    $response = $this->postJson('/subscribe', [
        'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
        'email' => 'user@example.com'
    ]);

    if ($response->status() === 500) {
        dd($response->getContent());
    }

    $response->assertStatus(200)->assertJson([
        'message' => 'Subscription successful. Please check your email to verify.'
    ]);
}
}
