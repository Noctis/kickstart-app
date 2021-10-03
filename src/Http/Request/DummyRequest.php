<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\AbstractRequest;

final class DummyRequest extends AbstractRequest
{
    public function getFoo(): string
    {
        return 'foo';
    }
}
