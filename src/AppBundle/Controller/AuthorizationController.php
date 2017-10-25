<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * Class AuthorizationController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class AuthorizationController extends Controller
{
    /**
     * ### Example Response ###
     *     {
     *         "token" : <token>,
     *         "role" : "ROLE_USER"
     *     }
     *
     *     Other roles are: ROLE_ADMIN or ROLE_TRAINER
     *
     * @Route("/api/login", name="login", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used for user authentication. Provides the token and the user role if credentials are valid",
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
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        $user =  $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
                $tokenManager = $this->get('lexik_jwt_authentication.jwt_manager');
                return new JsonResponse(
                    [
                        'token' => $tokenManager->create($user),
                        'role' => $this->getHighestRole($user)
                    ],
                    200
                );
            }
        }

        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }

    /**
     * Returns the highest user role
     *
     * @param User $user
     *
     * @return string
     */
    protected function getHighestRole(User $user) : string
    {
        $userRoles = $user->getRoles();
        $rolesSortedByImportance = ['ROLE_ADMIN', 'ROLE_TRAINER'];
        foreach ($rolesSortedByImportance as $role)
        {
            if (in_array($role, $userRoles))
            {
                return $role;
            }
        }

        return 'ROLE_USER';
    }
}
