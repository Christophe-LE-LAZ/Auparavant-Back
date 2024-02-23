<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 *
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function findMemoriesWithPlace(Place $place, int $memoryId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, m')
            ->leftJoin('p.memories', 'm')
            ->where('p = :place')
            ->andWhere('m.id != :memoryId')  // Exclure le souvenir actuel
            ->setParameter('place', $place)
            ->setParameter('memoryId', $memoryId)
            ->getQuery()
            ->getResult();
    }

    public function findByOrderAlphabeticalPlace($direction): array
    {
        $sql = "SELECT
        p.id as placeId, p.name as placeName, p.type as type,
        m.*,
        l.street as street, l.zipcode as zipcode, l.city as city,
        u.firstname as userFirstName, u.lastname as userLastName
    FROM place p
    LEFT JOIN memory m ON p.id = m.place_id
    LEFT JOIN location l ON m.location_id = l.id
    LEFT JOIN user u ON m.user_id = u.id

    ORDER BY
        p.name $direction";
    
        $conn = $this->getEntityManager()->getConnection();
        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Place[] Returns an array of Place objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Place
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
