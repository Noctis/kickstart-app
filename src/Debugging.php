<?php

declare(strict_types=1);

namespace App;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

final class Debugging
{
    private static ?Whoops $whoops = null;

    public static function on(): void
    {
        self::boot();

        ini_set('display_errors', 'On');

        /** @psalm-suppress PossiblyNullReference */
        self::$whoops->register();
    }

    public static function off(): void
    {
        self::boot();

        ini_set('display_errors', 'Off');

        /** @psalm-suppress PossiblyNullReference */
        self::$whoops->unregister();
    }

    private static function boot(): void
    {
        if (self::$whoops === null) {
            self::$whoops = new Whoops();
            self::$whoops->pushHandler(
                new PrettyPageHandler()
            );
        }
    }
}
