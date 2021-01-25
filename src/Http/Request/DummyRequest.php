<?php declare(strict_types=1);
namespace App\Http\Request;

use App\Service\DummyServiceInterface;
use Noctis\KickStart\Http\Request\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;

final class DummyRequest extends AbstractRequest
{
    private DummyServiceInterface $dummyService;

    public function __construct(DummyServiceInterface $dummyService, Request $request)
    {
        parent::__construct($request);

        $this->dummyService = $dummyService;
    }

    public function getFoo(): string
    {
        return $this->dummyService
            ->foo();
    }
}