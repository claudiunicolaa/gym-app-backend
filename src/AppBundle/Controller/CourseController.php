<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

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
     *              "capacity" : "30"
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
     *              "capacity" : "25"
     *         }
     *     }
     *
     *     Other roles are: ROLE_ADMIN or ROLE_TRAINER
     *
     * @todo Implement this method
     *
     * @Route("/api/course/get", name="course_get", methods={"GET"})
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
     *      {"name"="my_courses", "dataType"="boolean", "description"="Returns the courses the current user is registered to. Optional"},
     *      {"name"="courses_I_am_training", "dataType"="boolean", "description"="Returns the courses the current user is training. Optional"},
     *      {"name"="interval_start", "dataType"="timestamp", "description"="Returns the courses that start before the given time. Optional"},
 *          {"name"="interval_stop", "dataType"="timestamp", "description"="Returns the courses that start until the given time. Optional"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function myCoursesAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/create", name="course_create", methods={"POST"})
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
     *      {"name"="capacity", "dataType"="int"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function createAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/register", name="course_register", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to register to a course. In the request send the course id. Use the status code to understand the output. No JSON provided.",
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
    public function registerAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/update", name="course_update", methods={"PUT"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course update. Only an admin or the trainer can update the course. Use the status code to understand the output. No JSON provided.",
     *  section="Course",
     *  filters={
     *      {"name"="id", "dataType"="int"},
     *      {"name"="eventDate", "dataType"="timestamp"},
     *      {"name"="capacity", "dataType"="int"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing or given user did
     *          not create the course or is not an admin so he can't modify it"
     *  }
     *  )
     */
    public function updateAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/delete", name="course_delete", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for course removal. Use the course id for the removal. Use the status code to understand the output. No JSON provided.",
     *  section="Course",
     *  filters={
     *      {"name"="id", "dataType"="int"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing or given user did
     *          not create the course or is not an admin so he can't delete it"
     *  }
     *  )
     */
    public function deleteAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/course/unregister", name="course_unregister", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to unregister from a course. In the request send the course id. Use the status code to understand the output. No JSON provided.",
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
    public function unregisterAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }
}
