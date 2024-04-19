<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Exception\CompanyNotFoundException;
use App\Model\CompanyDTO;
use App\Repository\CompanyRepository;
use App\Transformer\CompanyTransformer;
use Doctrine\ORM\EntityManagerInterface;

class CompanyAPIService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository $companyRepository,
    ) {
    }

    public function upsertCompany(CompanyDTO $apiCompany, Company $company): Company
    {
        $parentCompany = $this->getCompanyById($apiCompany->parent_company_id, 'ParentCompanyId');
        $transformer = new CompanyTransformer($company, $apiCompany);
        $company = $transformer->transform($parentCompany);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $company;
    }

    public function deleteCompany(Company $company): void
    {
        $this->entityManager->remove($company);
        $this->entityManager->flush();
    }

    public function getCompanyById(string $companyId, string $field): Company
    {
        $company = $this->companyRepository->find($companyId);
        if (!$company) {
            throw new CompanyNotFoundException($companyId, $field);
        }

        return $company;
    }
}