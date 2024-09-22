<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            // Получаем и очищаем данные
            $url = mb_convert_encoding($request->input('url'), 'UTF-8', 'auto');
            $email = mb_convert_encoding($request->input('email'), 'UTF-8', 'auto');

            // Создаем подписку
            $subscription = Subscription::create([
                'url' => $url,
                'email' => $email,
            ]);

            // Логируем данные подписки
            Log::info('New subscription: ', ['url' => $url, 'email' => $email]);

            return response()->json(['message' => 'Subscription successful'], 200);
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('Subscription failed: ' . $e->getMessage());
            return response()->json(['message' => 'Subscription failed'], 500);
        }
    }

    private function fetchPriceFromOLX($url)
    {
        try {
            $client = new Client();
            $response = $client->get($url);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);
            $priceElement = $crawler->filter('.css-90xrc0 .css-10b0gli .css-okktvh .css-b1y81g')->first();
            $priceText = $priceElement->text();
            $price = preg_replace('/[^\d.]/', '', $priceText);

            return (float) $price;
        } catch (\Exception $e) {
            Log::error('Failed to fetch price: ' . $e->getMessage());
            return null; // Вернуть null или выбросить исключение, если нужно
        }
    }
}
