<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\Request;
use Psr\Http\Message\ServerRequestInterface;

final class DummyRequest extends Request implements ServerRequestInterface
{
    public function getFoo(): string
    {
        return 'foo';
    }
}
