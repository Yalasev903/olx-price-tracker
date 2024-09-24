<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\URL;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

            // Проверяем, существует ли уже подписка
            $existingSubscription = Subscription::where('url', $url)
                                                 ->where('email', $email)
                                                 ->first();

            if ($existingSubscription) {
                return response()->json(['message' => 'You are already subscribed to this URL.'], 409);
            }

            // Создаем подписку, добавляем токен подтверждения
            $subscription = Subscription::create([
                'url' => $url,
                'email' => $email,
                'verification_token' => Str::random(32),
                'is_verified' => false, // Подписка еще не подтверждена
            ]);

            // Логируем данные подписки
            Log::info('New subscription: ', ['url' => $url, 'email' => $email]);

            // Отправляем email с ссылкой на подтверждение
            Mail::to($email)->send(new VerificationEmail($subscription));

            return response()->json(['message' => 'Subscription successful. Please check your email to verify.'], 200);
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

    public function sendVerificationEmail($email, $subscriptionId, $token)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'subscription.verify', // маршрут для подтверждения
            now()->addMinutes(30), // Время действия ссылки
            ['id' => $subscriptionId, 'token' => $token] // передаем токен
        );
    
        Mail::to($email)->send(new \App\Mail\VerificationEmail($verificationUrl));
    }

    public function verify($id, $token, Request $request)
    {
        // Находим подписку или возвращаем 404 ошибку
        $subscription = Subscription::findOrFail($id);
    
        // Проверяем токен и его валидность
        if ($subscription->verification_token !== $token) {
            return redirect('/')->with('error', 'Invalid verification token.');
        }
    
        // Проверяем, была ли подписка уже подтверждена
        if ($subscription->is_verified) {
            return redirect('/')->with('message', 'Subscription already confirmed!');
        }
    
        // Подтверждаем подписку
        $subscription->is_verified = true;
        $subscription->save();
    
        return redirect('/')->with('message', 'Subscription confirmed!');
    }
}
