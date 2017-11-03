<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
     * @Route("/api/user", name="user_get", methods={"GET"})
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
    public function getUserAction() : JsonResponse
    {
        $loggedUser = $this->getUser();

        return new JsonResponse(
            [
                'id' => $loggedUser->getId(),
                'fullName' => $loggedUser->getFullName(),
                'email' => $loggedUser->getEmail(),
                'picture' => $loggedUser->getPicture()
            ],
            200
        );
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

    /**
     * ### Example Response ###
     *      {
     *         "id" : "1",
     *         "username" : "smith_adam",
     *         "email" : "smithadam@gmail.com"
     *     }
     *
     * @todo Implement this method
     *
     * @Route("/api/user", name="user_post", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used to register a user",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is not valid",
     *      401="Returned when the request is valid, but the email or username given already exist in the database"
     *  }
     *  )
     */
    public function registerUserAction(Request $request) : JsonResponse
    {
        $username   = $request->get('username');
        $password   = $request->get('password');
        $email      = $request->get('email');
        $firstName  = $request->get('firstName');
        $lastName   = $request->get('lastName');
        $picture    = $request->get('picture');

        if (!isset($email) || !isset($password) || !isset($username)) {
            return new JsonResponse(['error' => 'Missing email, password or username'], 400);
        }

        $possibleUserByEmail = $this->get('fos_user.user_manager')->findUserByEmail($email);
        $possibleUserByUsername = $this->get('fos_user.user_manager')->findUserByUsername($username);

        if ($possibleUserByEmail || $possibleUserByUsername) {
            return new JsonResponse(['error' => 'User already exists'], 401);
        }

        $userManager    = $this->get('fos_user.user_manager');
        $user           = $userManager->createUser();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPicture($picture);
        $user->setPassword($password);
        $user->addRole("ROLE_USER");
        $user->setEnabled(true);

        $userManager->updateUser($user);

        return new JsonResponse(
            [
                'id' => $user->getId(),
                'username' => $username,
                'email' => $email
            ],
            200
        );

    }
}