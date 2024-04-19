<?php
namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyAndStationsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $companies = [
            'A' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'B' => [1, 2, 3, 4, 5],
            'C' => [1, 2],
        ];

        foreach ($companies as $companyName => $stations) {
            $id = $faker->uuid();
            $company = (new Company())
                ->setId($id)
                ->setName($companyName)
                ->setParentCompany(null)
                ->setCreatedAt(new \DateTimeImmutable('now'))
                ->setUpdatedAt(new \DateTimeImmutable('now'));

            $manager->persist($company);

            foreach ($stations as $stationId) {
                $station = (new Station())
                    ->setAddress($faker->streetAddress())
                    ->setName(sprintf("Station %s -> %s", $companyName, $stationId))
                    ->setLatitude($faker->latitude(44.3, 44.5))
                    ->setLongitude($faker->longitude(25.97, 26.22))
                    ->setCompany($company)
                    ->setCreatedAt(new \DateTimeImmutable('now'))
                    ->setUpdatedAt(new \DateTimeImmutable('now'));

                $manager->persist($station);
            }
        }

        $manager->flush();
    }
}
