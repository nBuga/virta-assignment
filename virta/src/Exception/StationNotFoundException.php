<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StationNotFoundException extends HttpException
{
    public function __construct(string $stationId)
    {
        parent::__construct(
            $this->getStatusCode(),
            sprintf("Station with id %s not found!", $stationId)
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}