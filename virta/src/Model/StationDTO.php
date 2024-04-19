<?php
declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class StationDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,

        #[Assert\NotBlank]
        public string $company_id,

        #[Assert\Range(min: 25.97, max: 26.22)]
        public float $longitude,

        #[Assert\Range(min: 44.3, max: 44.5)]
        public float $latitude,

        #[Assert\NotBlank]
        public string $address,
    ) {
    }
}