<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @param $value
     * @return Course[] Returns an array of Course objects
     */
    public function findCourseAgainstUser($value): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.traineroruserid = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $qryString
     * @return array
     * @throws DBALException
     */
    public function findCourseSearch($qryString): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id,topic,description,startdatetime,enddatetime FROM course where topic LIKE '%$qryString%'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $excludeCourse
     * @return int|mixed|string
     */
    public function availableCourseList($excludeCourse){
        $qb = $this->createQueryBuilder('c');
        return $qb->where($qb->expr()->notIn('c.id', $excludeCourse))->getQuery()->getResult();
    }

    /**
     * @return mixed[]
     * @throws DBALException
     */
    public function findReservedUser($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select COUNT(rc.course_id) AS course_count,c.limitedseats,c.topic FROM reservation_course rc LEFT JOIN reservation_user ru ON ru.reservation_id =rc.reservation_id LEFT JOIN course c ON rc.course_id = c.id  where c.traineroruserid_id =$id  GROUP BY c.id;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
