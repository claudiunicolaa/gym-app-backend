<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Exception\CourseValidationException;
use AppBundle\Repository\CourseRepository;
use AppBundle\Services\Validator\CourseValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class CourseController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseController extends Controller
{
    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "trainer" : {
     *                  "id" : "1",
     *                  "fullName" : "Smith Adam",
     *                  "email" : "adamsmith@gmail.com",
     *                  "picture" : "https://i.imgur.com/NiCqGa3.jpg"
     *              },
     *              "eventDate" : "1508916731",
     *              "capacity" : "30",
     *              "name" : "Course A",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "registered_users" : "15"
     *         },
     *         {
     *              {
     *              "id" : "2",
     *              "trainer" : {
     *                  "id" : "2",
     *                  "fullName" : "Adam George",
     *                  "email" : "adamgeorge@gmail.com",
     *                  "picture" : "https://i.imgur.com/NiCqGa3.jpg"
     *              },
     *              "eventDate" : "1508916731",
     *              "capacity" : "25",
     *              "name" : "Course B",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "registered_users" : "25"
     *         }
     *     }
     *
     * @todo Implement this method
     *
     * @Route("/api/courses", name="courses_get", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns all courses that match the given filters.",
     *  section="Course",
     * filters={
     *      {"name"="users_courses", "dataType"="boolean", "description"="Returns the courses the current user is registered to. Optional"},
     *      {"name"="owned_courses", "dataType"="boolean", "description"="Returns the courses the current user is training. Optional"},
     *      {"name"="interval_start", "dataType"="timestamp", "description"="Returns the courses that start before the given time. Optional"},
     *      {"name"="interval_stop", "dataType"="timestamp", "description"="Returns the courses that start until the given time. Optional"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getCoursesAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "trainer" : {
     *                  "id" : "1",
     *                  "fullName" : "Smith Adam",
     *                  "email" : "adamsmith@gmail.com",
     *                  "picture" : "https://i.imgur.com/NiCqGa3.jpg"
     *              },
     *              "eventDate" : "1508916731",
     *              "capacity" : "30",
     *              "name" : "Course A",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "registered_users" : "15"
     *         }
     *     }
     *
     * @Route("/api/course/{id}", name="course_get", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns the course with the given id.",
     *  section="Course",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getCourseAction(Request $request, ?Course $course) : JsonResponse
    {
        if (null === $course) {
            return new JsonResponse(['error' => 'Course with given id doesn\'t exist'], 400);
        }

        return new JsonResponse($course->toArray(), 200);
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course", name="course_create", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course creation. The assigned trainer will be the user that makes the request. Use the status code to understand the output. No JSON provided.",
     *  section="Course",
     *  filters={
     *      {"name"="eventDate", "dataType"="timestamp"},
     *      {"name"="capacity", "dataType"="int"},
     *      {"name"="image", "dataType"="string", "description" : "Optional"},
     *      {"name"="name", "dataType"="string"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function createCourseAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/subscription", name="course_subscribe", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to subscribe to a course. In the request send the course id. Use the status code to understand the output. No JSON provided.",
     *  section="Course",
     *  filters={
     *      {"name"="id", "dataType"="int"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function subscribeAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @Route("/api/course/{id}", name="course_update", methods={"PUT"})
     *
     * @param Request $request
     * @param Course $course
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course update. Only an admin or the trainer can update the course.",
     *  section="Course",
     *  filters={
     *      {"name"="eventDate", "dataType"="timestamp", "description"="Optional"},
     *      {"name"="capacity", "dataType"="int", "description"="Optional"},
     *      {"name"="image", "dataType"="string", "description"="Optional"},
     *      {"name"="name", "dataType"="string", "description"="Optional"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      403="Returned when user did not create the course or is not an admin"
     *  }
     *  )
     */
    public function updateCourseAction(Request $request, ?Course $course) : JsonResponse
    {
        $queryParameters = $request->query->all();

        if (null === $course) {
            return new JsonResponse(['error' => 'Course with given id doesn\'t exist'], 400);
        }

        $loggedUser = $this->getUser();
        if (!($course->getTrainer()->getId() === $loggedUser->getId()) &&
            !in_array('ROLE_ADMIN', $loggedUser->getRoles())
        ) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        try {
            $this->get(CourseValidator::class)->validate($queryParameters);
        } catch (CourseValidationException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($queryParameters as $key => $value) {
            $propertyAccessor->setValue($course, $key, $value);
        }

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('', 200);
    }

    /**
     * @Route("/api/course", name="course_delete", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course removal. Use the course id for the removal.",
     *  section="Course",
     *  filters={
     *      {"name"="id", "dataType"="int"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      403="Returned when user did not create the course and is not an admin"
     *  }
     *  )
     */
    public function deleteCourseAction(Request $request) : JsonResponse
    {
        $courseId = $request->get('id');
        if ($courseId === null) {
            return new JsonResponse(['error' => 'Missing parameter id'], 400);
        }

        /** @var Course $course */
        $course = $this->get(CourseRepository::class)->find($courseId);
        if ($course === null) {
            return new JsonResponse(['error' => 'Course with given id doesn\'t exist'], 400);
        }

        $loggedUser = $this->getUser();
        if (!($course->getTrainer()->getId() === $loggedUser->getId()) &&
            !in_array('ROLE_ADMIN', $loggedUser->getRoles()))
        {
            return new JsonResponse(['error' => 'Not authorized'], 403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($course);
        $em->flush();

        return new JsonResponse('', 200);
    }

    /**
     * @Route("/api/course/{id}/subscription", name="course_unsubscribe", methods={"DELETE"})
     *
     * @param Course $course
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to unsubscribe from a course. In the request send the course id. Use the status code to understand the output. No JSON provided.",
     *  section="Course",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid. Course invalid or expired or user not subscribed.",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function unsubscribeAction(?Course $course) : JsonResponse
    {
        $loggedUser = $this->getUser();

        if (null === $course) {
            return new JsonResponse(['error' => 'Course with given id doesn\'t exist!'], 400);
        }

        if ($course->isInThePast()) {
            return new JsonResponse(['error' => 'Course has expired!'], 400);
        }

        if (!$course->getRegisteredUsers()->contains($loggedUser)) {
            return new JsonResponse(['error' => 'User not registered to this course!'], 400);
        }

        $course->removeRegisteredUser($loggedUser);
        $loggedUser->removeAttendingCourse($course);
        $em = $this->getDoctrine()->getManager();
        $em->persist($course);
        $em->persist($loggedUser);
        $em->flush();

        return new JsonResponse('', 200);
    }
}
