<?php

namespace App\Repository;

use App\Entity\HealthRecord;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

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

    public function examinationsCount(): int
    {
        $qb = $this->createQueryBuilder('hr');
        $qb->select('count(hr.id) numberOfExaminations');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getExaminationsInTimeRange(string $today): array
    {
        $now = new DateTime();
        $deadline = $this->timeRange($today);

        $qb = $this->createQueryBuilder('hr');
        $qb->select('hr')
            ->andWhere('hr.startedAt>=:now')
            ->setParameter('now', $now)
            ->andWhere('hr.finishedAt<:deadline')
            ->setParameter('deadline', $deadline)
            ->andWhere('hr.notifiedWeekBefore = 0');

        return $qb->getQuery()->getResult();
    }

    private function timeRange(string $range): DateTime|string
    {
        if($range === 'today'){
            return new DateTime('+1 day');
        }
        else if($range === 'next week'){
            return new DateTime('+7 days');
        }
        else{
            return 'wrong time range selected';
        }
    }

    /**
     * @throws Exception
     */
    public function getLastMonthHealthRecords(int $numericalMonth): array
    {
        $em = $this->getEntityManager();

        $sql = 'SELECT *
           FROM health_record
           WHERE MONTH(finished_at) = :month';

        $conn = $em->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindValue('month', $numericalMonth);

        return $stmt->execute()->fetchAll();

    }
}
