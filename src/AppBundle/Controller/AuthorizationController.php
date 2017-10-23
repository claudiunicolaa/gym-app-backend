<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthorizationController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class AuthorizationController extends Controller
{
    /**
     * @Route("/api/login", name="login", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for user authentication. Provides the token if credentials are valid",
     *  section="Authorization",
     *  filters={
     *      {"name"="email", "dataType"="string"},
     *      {"name"="password", "dataType"="string"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the credentials are invalid"
     *  }
     *  )
     */
    public function loginAction(Request $request) : JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');

        if (!isset($email) || !isset($password)) {
            return new JsonResponse(array('error' => 'Missing email or password'), 400);
        }

        $user =  $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
                $tokenManager = $this->get('lexik_jwt_authentication.jwt_manager');
                return new JsonResponse(array('token' => $tokenManager->create($user)), 200);
            }
        }

        return new JsonResponse(array('error' => 'Invalid credentials'), 401);
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for user registration. Returns the status code 200 if successful",
     *  section="Authorization",
     *  filters={
     *      {"name"="email", "dataType"="string"},
     *      {"name"="firstName", "dataType"="string"},
     *      {"name"="lastName", "dataType"="string"},
     *      {"name"="picture", "dataType"="string"},
     *      {"name"="username", "dataType"="string"},
     *      {"name"="password", "dataType"="string"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the credentials are invalid"
     *  }
     *  )
     */
    public function registerAction(Request $request) : JsonResponse
    {
        $email = $request->get('email');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $picture = $request->get('picture');
        $username = $request->get('username');
        $password = $request->get('password');

        if (!isset($email) || !isset($password) || !isset($username)) {
            return new JsonResponse(array('error' => 'Missing email or username or password'), 400);
        }

        $user =  $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user) {
            return new JsonResponse(array('error' => 'User already registered'), 401);
        }

        $em = $this->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPicture($picture);

        $em->persist($user);
        $em->flush($user);

        return new JsonResponse(array('message' => 'User successfully registered'), 200);


    }
}
