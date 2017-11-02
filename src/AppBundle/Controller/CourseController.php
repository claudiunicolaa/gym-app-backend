<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Exception\CourseValidationException;
use AppBundle\Exception\CourseRepositoryException;
use AppBundle\Repository\CourseRepository;
use AppBundle\Services\Validator\CourseFiltersValidator;
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
     *      {"name"="users_courses", "dataType"="string", "description"="Returns the courses the current user is registered to. Optional. Values: true or false"},
     *      {"name"="owned_courses", "dataType"="string", "description"="Returns the courses the current user is training. Optional. Values: true or false"},
     *      {"name"="interval_start", "dataType"="timestamp", "description"="Returns the courses that start before the given time. Optional"},
     *      {"name"="interval_stop", "dataType"="timestamp", "description"="Returns the courses that start until the given time. Optional"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      400="Returned when the request is invalid"
     *  }
     *  )
     */
    public function getCoursesAction(Request $request) : JsonResponse
    {
        try {
            $this->get(CourseFiltersValidator::class)->validate($request->query->all());
            $courses = $this
                ->get(CourseRepository::class)
                ->getFilteredCourses(
                    $this->getUser(),
                    $request->query->all()
                )
            ;

            return new JsonResponse($this->formatResult($courses), 200);
        } catch (CourseRepositoryException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }
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
     * @param Course    $course
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
    public function getCourseAction(?Course $course) : JsonResponse
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
            return new JsonResponse(['error' => 'Not Authorized'], 403);
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
     * @Route("/api/course/{id}", name="course_delete", methods={"DELETE"})
     *
     * @param Course $course
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course removal. Use the course id for the removal.",
     *  section="Course",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      403="Returned when user did not create the course and is not an admin"
     *  }
     *  )
     */
    public function deleteCourseAction(?Course $course) : JsonResponse
    {
        if (null === $course) {
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
     * @todo Implement this method
     *
     * @Route("/api/course/subscription", name="course_unsubscribe", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to unsubscribe from a course. In the request send the course id. Use the status code to understand the output. No JSON provided.",
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
    public function unsubscribeAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function formatResult(array $data) : array
    {
        $result = [];

        foreach ($data as $key => $courseData) {
            $result[$key]['trainer'] = [];
            $result[$key]['trainer']['id'] = $courseData["trainer_id"];
            $result[$key]['trainer']['fullName'] = $courseData['lastName'] . ' ' .$courseData['firstName'];
            $result[$key]['trainer']['email'] = $courseData['email'];
            $result[$key]['trainer']['picture'] = $courseData['picture'];
            $result[$key]['eventDate'] = $courseData['eventDate']->getTimestamp();
            $result[$key]['id'] = $courseData['id'];
            $result[$key]['capacity'] = $courseData['capacity'];
            $result[$key]['name'] = $courseData['name'];
            $result[$key]['image'] = $courseData['image'];
            $result[$key]['registered_users'] = $courseData['registered_users'];
        }

        return $result;
    }
}
