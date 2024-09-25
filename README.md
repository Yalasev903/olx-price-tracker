Установка та налаштування сервісу для відстеження зміни ціни на OLX

Діаграма сервісу:

https://app.diagrams.net/?src=about#HYalasev903%2Folx-price-tracker%2Fmaster%2F%D0%9E%D0%9B%D0%A5%20%D0%BF%D0%B0%D1%80%D1%81%D0%B5%D1%80%20%D1%86%D0%B5%D0%BD.drawio#%7B%22pageId%22%3A%22zW4MrdNnY_COWWRTuxkU%22%7D

Опис сервісу
Сервіс призначений для моніторингу зміни цін на оголошення на платформі OLX. Користувачі можуть підписатися на зміни цін, вказавши посилання на оголошення та свій email. При зміні ціни сервіс надсилає сповіщення на вказану адресу електронної пошти. Якщо кілька користувачів підписалися на одне й те саме оголошення, сервіс оптимізує перевірку, щоб уникнути зайвих запитів.

Основні функції сервісу
Підписка на зміни ціни: HTTP метод для підписки на зміни ціни з вказанням URL оголошення та email для сповіщень.
Відстеження змін: Сервіс періодично перевіряє ціну оголошення та надсилає сповіщення при зміні.
Оптимізація запитів: Якщо кілька користувачів підписані на одне оголошення, перевірка ціни здійснюється лише один раз.
Схема роботи сервісу

Установка
Клонування репозиторію

git clone https://github.com/Yalasev903/olx-price-tracker.git
cd olx-price-tracker
Встановлення залежностей
Переконайтеся, що у вас встановлений Composer. Потім виконайте команду:


composer install
Налаштування середовища
Скопіюйте файл .env.example в .env:


cp .env.example .env
Відкрийте файл .env і налаштуйте параметри бази даних та поштового сервісу.

Створення бази даних
Запустіть міграції для створення таблиць:


php artisan migrate
Запуск сервісу в Docker
Переконайтеся, що у вас встановлені Docker та Docker Compose. Запустіть контейнери:


docker-compose up -d
Після цього сервіс буде доступний за адресою http://localhost:9000.

Функціональність
Підписка на зміни ціни
Метод для підписки доступний за адресою /subscribe. Ви можете надіслати POST-запит з JSON-даними:

json
{
    "url": "https://www.olx.ua/d/uk/obyavlenie/example",
    "email": "user@example.com"
}
Відстеження змін
Сервіс періодично перевіряє зміни цін на підписані оголошення і надсилає сповіщення на вказані email.

Відправка сповіщень
Сповіщення надсилаються за допомогою поштового сервісу, налаштованого у файлі .env.

Тестування
У проекті реалізовані автоматичні тести з покриттям понад 70%. Для запуску тестів використовуйте команду:


php artisan test

Запуск Cron для тестування зміни ціни на ОЛХ 

php artisan schedule:work

Данні для тесту на пошти та для файлу .env

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=dba045f134ccd8
MAIL_PASSWORD=9bd8a37f1c5f4f
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hi@demomailtrap.com"

Реалізація

При реалізації Використовуеться фреймворк Laravel тому що він більш адаптований для виконання заданих задач, та більш гнучкий для прискорення сервісу.

При реалізації використовувалися такі підходи:

Парсинг веб-сторінки
Переваги: Легкість в реалізації, можна швидко отримати потрібні дані.
Недоліки: Залежність від структури сторінки, можливі зміни в розмітці можуть призвести до поломки парсера.
Використання API
Переваги: Більш надійний спосіб отримання даних, оскільки API, як правило, стабільніший.
Недоліки: Може знадобитися більше часу на дослідження та реалізацію.
Вибір
Для цього проекту був обраний метод парсингу веб-сторінки, оскільки він простіший в реалізації і дозволяє швидко отримати дані. У майбутньому можливе додавання API, якщо OLX надасть офіційне рішення.

Репозиторій
Посилання на репозиторій з кодом: GitHub - olx-price-tracker

Висновок
Сервіс успішно реалізує завдання з відстеження зміни цін на оголошення на OLX, надає можливість підписатися на сповіщення та надсилає email-повідомлення користувачам. Завдяки тестам та використанню Docker, сервіс легко розгортається і перевіряється.


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
