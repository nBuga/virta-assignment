<?php
declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CompanyDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public string $parent_company_id
    ) {
    }
}