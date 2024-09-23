<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->post('/subscribe', [
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/unversalna-kolyaska-2-v-1-carrello-sigma-crl-6509-fog-grey-IDVdUyX.html',
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', ['email' => 'user@example.com']);
    }
}
