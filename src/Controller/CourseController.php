<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\CourseType;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CourseController extends AbstractController
{
    //Current Symfony versions (Symfony 4, Symfony >=3.2)
    //Since Symfony >=3.2 you can simply expect a UserInterface implementation to be injected to your controller action directly.
    //we can then call getId() to retrieve user's identifier:

    /**
     * @param EntityManagerInterface $entityManager
     * @return ObjectRepository
     */
    public function courseRepository(EntityManagerInterface $entityManager): ObjectRepository
    {
        return $entityManager->getRepository(Course::class);
    }

    /**
     * @Route ("/course_list", name="course_list")
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    public function index(EntityManagerInterface $em, UserInterface $user): Response
    {

        $role = implode(" ", $user->getRoles());

        if ($role == 'ROLE_ADMIN') {
            $allCourseList = $this->courseRepository($em)->findCourseAgainstUser($user->getId());
            return $this->render('course/index.html.twig', [
                'courses' => $allCourseList,
            ]);
        }

        if ($role == 'ROLE_USER') {
            $excludeCourse = array();
            $reservedCountList = $em->getRepository(Reservation::class)->slotsReservedCount();
            foreach ($reservedCountList as $key => $perms) {
                if ($perms['limitedseats'] <= $perms['course_count']) {
                    array_push($excludeCourse, $perms['courseId']);
                }
            }
            if(!empty($excludeCourse)){
                $allCourseList = $this->courseRepository($em)->availableCourseList($excludeCourse);
            }else{
                $allCourseList = $this->courseRepository($em)->findAll();
            }

            return $this->render('course_search/index.html.twig', [
                'courses' => $allCourseList,
                'upcomingreserved' => false
            ]);
        }
    }

    /**
     * @Route("/create_course", name="create_course")
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user, EntityManagerInterface $em): Response
    {
        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userObj = $em->getRepository(User::class)->find($user->getId());
            $course->setTraineroruserid($userObj);
            $em->persist($course);
            $em->flush();
            return $this->redirectToRoute('course_list');
        }
        return $this->render('course/create.html.twig', [
            'form' => $form->createView(),
            'button_text' => 'Create'
        ]);
    }

    /**
     * @Route ("/course_update/{id}", defaults={"id" = 0},name="course_update")
     * @param Request $request
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */

    public function update(Request $request, $id, EntityManagerInterface $em)
    {
        $course = $this->courseRepository($em)->find($id);
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();
            return $this->redirectToRoute('course_list');
        }
        return $this->render('course/create.html.twig', [
            'form' => $form->createView(),
            'button_text' => 'Update'
        ]);
    }

    /**
     * @Route ("/course_delete/{id}", defaults={"id" = 0}, name="course_delete")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */

    public function delete($id, EntityManagerInterface $em): RedirectResponse
    {

        $deleteCourse = $this->courseRepository($em)->find($id);
        if (!$deleteCourse) {
            throw $this->createNotFoundException(
                'There are no course with the following id: ' . $id
            );
        }
        $em->remove($deleteCourse);
        $em->flush();

        return $this->redirectToRoute('course_list');
    }

    /**
     * @Route ("/course_reserved", name="course_reserved")
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */

    public function view(EntityManagerInterface $em,UserInterface $user): Response
    {

        $allCourseList = $this->courseRepository($em)->findReservedUser($user->getId());
        //print_r($allCourseList);die('123');
        return $this->render('course/view.html.twig', [
            'courses' => $allCourseList
        ]);
    }
}
