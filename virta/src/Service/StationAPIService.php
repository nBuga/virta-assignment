<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Station;
use App\Model\StationDTO;
use App\Transformer\StationTransformer;
use Doctrine\ORM\EntityManagerInterface;

class StationAPIService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyAPIService $companyAPIService
    ) {
    }

    public function upsertStation(StationDTO $apiStation, Station $station): Station
    {
        $company = $this->companyAPIService->getCompanyById($apiStation->company_id, 'CompanyId');
        $transformer = new StationTransformer($station, $apiStation);
        $station = $transformer->transform($company);

        $this->entityManager->persist($station);
        $this->entityManager->flush();

        return $station;
    }

    public function deleteStation(Station $station): void
    {
        $this->entityManager->remove($station);
        $this->entityManager->flush();
    }
}