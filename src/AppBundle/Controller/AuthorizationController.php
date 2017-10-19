<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @Route("/api/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        if (!isset($email) || !isset($password)) {
            return new JsonResponse(array('error' => 'Missing email or password'));
        }

        $user =  $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
                $tokenManager = $this->get('lexik_jwt_authentication.jwt_manager');
                return new JsonResponse(array('token' => $tokenManager->create($user)));
            }
        }

        return new JsonResponse(array('error' => 'Invalid credentials'));
    }
}
