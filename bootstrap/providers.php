<?php

/*

OUTPUT dd(ServiceProvider::defaultProviders()); Laravel 11 marzo 2025

0 => "Illuminate\Auth\AuthServiceProvider"
1 => "Illuminate\Broadcasting\BroadcastServiceProvider"
2 => "Illuminate\Bus\BusServiceProvider"
3 => "Illuminate\Cache\CacheServiceProvider"
4 => "Illuminate\Foundation\Providers\ConsoleSupportServiceProvider"
5 => "Illuminate\Concurrency\ConcurrencyServiceProvider"
6 => "Illuminate\Cookie\CookieServiceProvider"
7 => "Illuminate\Database\DatabaseServiceProvider"
8 => "Illuminate\Encryption\EncryptionServiceProvider"
9 => "Illuminate\Filesystem\FilesystemServiceProvider"
10 => "Illuminate\Foundation\Providers\FoundationServiceProvider"
11 => "Illuminate\Hashing\HashServiceProvider"
12 => "Illuminate\Mail\MailServiceProvider"
13 => "Illuminate\Notifications\NotificationServiceProvider"
14 => "Illuminate\Pagination\PaginationServiceProvider"
15 => "Illuminate\Auth\Passwords\PasswordResetServiceProvider"
16 => "Illuminate\Pipeline\PipelineServiceProvider"
17 => "Illuminate\Queue\QueueServiceProvider"
18 => "Illuminate\Redis\RedisServiceProvider"
19 => "Illuminate\Session\SessionServiceProvider"
20 => "Illuminate\Translation\TranslationServiceProvider"
21 => "Illuminate\Validation\ValidationServiceProvider"
22 => "Illuminate\View\ViewServiceProvider"

*/

use App\Providers\AppServiceProvider;

return [
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    //Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    //Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    AppServiceProvider::class
];