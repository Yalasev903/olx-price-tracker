<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Jobs\CheckPriceJob;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ваши middleware
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Обработка исключений
    })
    ->booted(function () {
        // Регистрация периодической задачи
        $schedule = app(Schedule::class);

        // Задача для CheckPriceJob каждые 10 минут
        $schedule->job(new CheckPriceJob)->everyTenMinutes();

        // Добавление команды check:price каждые 5 минут
        $schedule->command('check:price')->everyFiveMinutes();
    })
    ->create();
