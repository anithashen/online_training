<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CourseSearchController extends AbstractController
{
    /**
     * @Route("/upcoming_reserved_course", name="upcoming_reserved_course")
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function index(EntityManagerInterface $em, UserInterface $user): Response
    {
        $upcomingReservedList = $em->getRepository(Reservation::class)->findUpcomingReservedCourse($user->getId());
        return $this->render('course_search/index.html.twig', [
            'courses' => $upcomingReservedList,
            'upcomingreserved' => true
        ]);

    }

    /**
     * @Route("/reserved_course", name="reserved_course")
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $course_Id = $_POST['courseId'];
        $userObj = $em->getRepository(User::class)->findOneBy(array('id' => $user->getId()));
        $courseObj = $em->getRepository(Course::class)->findOneBy(array('id' => $course_Id));
        $allow = $em->getRepository(Reservation::class)->sameCourseExistOrNot($user->getId(),$course_Id);
        if ($allow == true) {
            $reservation->addEmployeeid($userObj);
            $reservation->addCourseid($courseObj);
            $em->persist($reservation);
            $em->flush();
        }

        return new Response(
            'success',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route ("/upcoming_reservation_delete/{id}", defaults={"id" = 0}, name="upcoming_reservation_delete")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */

    public function deleteAction($id, EntityManagerInterface $em): RedirectResponse
    {

        $deleteCourse = $em->getRepository(Reservation::class)->find($id);
        if (!$deleteCourse) {
            throw $this->createNotFoundException(
                'There are no course with the following id: ' . $id
            );
        }
        list($user_id, $course_id) = $em->getRepository(Reservation::class)->fetchUserAndCourseId($id);
        $em->getRepository(Reservation::class)->removeReservedUser($user_id, $id);
        $em->getRepository(Reservation::class)->removeReservedCourse($course_id, $id);
        $em->remove($deleteCourse);
        $em->flush();

        return $this->redirectToRoute('course_list');
    }

    /**
     * @Route("/course_search_word", name="course_search_word")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function filterCourse(EntityManagerInterface $em): Response
    {
        $qryString = $_POST['data'];
        $repository = $em->getRepository(Course::class);
        $allCourseList = $repository->findCourseSearch($qryString);
        return new Response(json_encode($allCourseList));
    }
}
