<?php
declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Company;
use App\Entity\Station;
use App\Model\StationDTO;

readonly class StationTransformer
{
    public function __construct(private Station $station, private StationDTO $apiStation)
    {
    }

    public function transform(Company $company): Station
    {
        $this->station->setAddress($this->apiStation->address);
        $this->station->setLatitude($this->apiStation->latitude);
        $this->station->setLongitude($this->apiStation->longitude);
        $this->station->setName($this->apiStation->name);
        $this->station->setCompany($company);
        $this->station->setCreatedAt(new \DateTimeImmutable('now'));
        $this->station->setUpdatedAt(new \DateTimeImmutable('now'));

        return $this->station;
    }
}