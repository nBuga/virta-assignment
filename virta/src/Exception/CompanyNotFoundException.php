<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CompanyNotFoundException extends HttpException
{
    public function __construct(string $companyId, string $field)
    {
        parent::__construct(
            $this->getStatusCode(),
            sprintf("%s with id %s not found!", $field, $companyId)
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}