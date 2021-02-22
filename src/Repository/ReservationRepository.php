<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @param $user_id
     * @return int|mixed|string
     * @throws DBALException
     */
    public function findUpcomingReservedCourse($user_id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select rc.reservation_id as id,topic,description,startdatetime,enddatetime FROM course c JOIN reservation_course rc ON c.id = rc.course_id  join reservation_user ru On ru.reservation_id = rc.reservation_id where ru.user_id =$user_id and (c.startdatetime > NOW() or c.enddatetime > NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @return int|mixed|string
     * @throws DBALException
     */
    public function slotsReservedCount()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select COUNT(rc.course_id) AS course_count,c.id as courseId,c.limitedseats FROM reservation_course rc LEFT JOIN reservation_user ru ON ru.reservation_id =rc.reservation_id JOIN course c ON rc.course_id = c.id GROUP BY c.id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $user_id
     * @param $reserve_id
     * @throws DBALException
     */
    public function removeReservedUser($user_id, $reserve_id)
    {
        $removeReservationUser = $this->getEntityManager()->getConnection()
            ->prepare("delete from `reservation_user` where user_id = ? and reservation_id = ?");
        $removeReservationUser->bindValue(1, $user_id);
        $removeReservationUser->bindValue(2, $reserve_id);
        $removeReservationUser->execute();
        $removeReservationUser->closeCursor();
    }

    /**
     * @param $course_id
     * @param $reserve_id
     * @throws DBALException
     */
    public function removeReservedCourse($course_id, $reserve_id)
    {
        $removeReservationUser = $this->getEntityManager()->getConnection()
            ->prepare("delete from `reservation_course` where course_id = ? and reservation_id = ?");
        $removeReservationUser->bindValue(1, $course_id);
        $removeReservationUser->bindValue(2, $reserve_id);
        $removeReservationUser->execute();
        $removeReservationUser->closeCursor();
    }

    /**
     * @param $id
     * @return array
     * @throws DBALException
     */
    public function fetchUserAndCourseId($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select rc.course_id as course_id, ru.user_id as user_id from reservation_user ru join reservation_course rc on rc.reservation_id = ru.reservation_id where ru.reservation_id=$id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return array($result[0]['user_id'],$result[0]['course_id']);
    }

    /**
     * @param $userId
     * @param $courseId
     * @return bool
     * @throws DBALException
     */
    public function sameCourseExistOrNot($userId, $courseId)
    {
        $allow = true;
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select r.id from reservation r join reservation_user ru on ru.reservation_id = r.id join reservation_course rc on rc.reservation_id = ru.reservation_id where rc.course_id=$courseId and ru.user_id=$userId";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (count($result) > 0) {
            $allow = false;
        }
        return $allow;
    }
}
