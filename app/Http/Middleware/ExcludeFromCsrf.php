<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExcludeFromCsrf
{
    /**
     * Маршруты, которые будут исключены из проверки CSRF
     *
     * @var array
     */
    protected $except = [
        'subscription/verify/*', // Добавляем исключение для маршрута верификации
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, не попадает ли текущий маршрут в список исключений
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                // Пропускаем проверку CSRF для этого маршрута
                return $next($request);
            }
        }

        // Если маршрут не исключен, продолжить выполнение с CSRF-проверкой
        return $next($request);
    }
}
