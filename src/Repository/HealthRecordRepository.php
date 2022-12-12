<?php

namespace App\Repository;

use App\Entity\HealthRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HealthRecord>
 *
 * @method HealthRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method HealthRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method HealthRecord[]    findAll()
 * @method HealthRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HealthRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealthRecord::class);
    }

    public function save(HealthRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HealthRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getVetPercentage(): array
    {
        $qb = $this->createQueryBuilder('hr');
        $qb->select(
              'vet.id',
              'vet.firstName',
              'vet.lastName',
              'count(hr.vet) examinations'
            )
            ->join('hr.vet', 'vet')
            ->groupBy('hr.vet')
            ->orderBy('hr.vet', 'desc');

        $count = $this->examinationsCount();
        $vetsWithPercentage = [];

        foreach ($qb->getQuery()->getResult() as $vet) {

            $percentage = 100*$vet['examinations'] / $count;
            //upper row is multiplying result of dividing number of examinations
            //by count of all examinations

            $vet += ['percentage' => number_format((float)$percentage, 2, '.', '')];
            //this, last item in assoc array object of each vet must be concatenated
            //on its end with '%' while displaying on the front end

            $vetsWithPercentage[] = $vet;
        }

        return $vetsWithPercentage;
    }

    public function examinationsCount(): int
    {
        $qb = $this->createQueryBuilder('hr');
        $qb->select('count(hr.id) numberOfExaminations');

        return $qb->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return HealthRecord[] Returns an array of HealthRecord objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
