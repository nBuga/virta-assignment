<?php

namespace App\Repository;

use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Station>
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    public function findWithinRadius(float $latitude, float $longitude, float $radius): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Station', 's');
        $rsm->addFieldResult('s', 'stationId', 'id');
        $rsm->addFieldResult('s', 'stationName', 'name');
        $rsm->addFieldResult('s', 'latitude', 'latitude');
        $rsm->addFieldResult('s', 'longitude', 'longitude');
        $rsm->addFieldResult('s', 'address', 'address');
        $rsm->addFieldResult('s', 'companyId', 'company');
        $rsm->addScalarResult('distance', 'distance');

        $rsm->addJoinedEntityResult('App\Entity\Company' , 'c', 's', 'company');
        $rsm->addFieldResult('c', 'companyId', 'id');
        $rsm->addFieldResult('c', 'companyName', 'name');
        $rsm->addFieldResult('c', 'stations', 'stations');

        $sql = "
            SELECT
               station.id as stationId, 
               company_id as companyId,
               station.name as stationName, 
               latitude,
               longitude,
               station.address,
               company.name as companyName,
               (
                   6371 * acos (
                      cos ( radians(:lat) )
                      * cos( radians( latitude ) )
                      * cos( radians( longitude ) - radians(:lon) )
                      + sin ( radians(:lat) )
                      * sin( radians( latitude ) )
                   )
                ) AS distance
            FROM station
            INNER JOIN company ON company.id = station.company_id
            HAVING distance < :radius
            ORDER BY distance;
        ";

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('lat', $latitude);
        $query->setParameter('lon', $longitude);
        $query->setParameter('radius', $radius);

        return $query->getResult();
    }

}
