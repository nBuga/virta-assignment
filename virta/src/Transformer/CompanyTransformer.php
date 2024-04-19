<?php
declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Company;
use App\Model\CompanyDTO;

readonly class CompanyTransformer
{
    public function __construct(
        private Company $company,
        private CompanyDTO $apiCompany
    ){
    }

    public function transform(Company $parentCompany): Company
    {
        $this->company->setName($this->apiCompany->name);
        $this->company->setParentCompany($parentCompany);
        $this->company->setCreatedAt(new \DateTimeImmutable('now'));
        $this->company->setUpdatedAt(new \DateTimeImmutable('now'));

        return $this->company;
    }
}