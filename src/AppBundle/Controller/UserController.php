<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

/**
 * Class UserController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class UserController extends Controller
{
    /**
     * ### Example Response ###
     *      {
     *         "id" : "1",
     *         "fullName" : "Smith Adam",
     *         "email" : "smithadam@gmail.com"
     *         "picture" : "https://i.imgur.com/NiCqGa3.jpg"
     *     }
     *
     * @todo Implement this method
     *
     * @Route("/api/user", name="user_get", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used to get information about the current user",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getUserAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }

    /**
     * @todo Implement this method
     *
     * @Route("/api/user", name="user_update", methods={"PUT"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used to update information about the current user. Use the status code to understand the output. No JSON provided.",
     *  section="User",
     *  filters={
     *      {"name"="fullName", "dataType"="string"},
     *      {"name"="picture", "dataType"="string"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function updateUserAction(Request $request) : JsonResponse
    {
        throw new NotImplementedException("Not implemented");
    }
}