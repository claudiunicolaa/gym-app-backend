<?php

namespace AppBundle\Controller;

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
}
