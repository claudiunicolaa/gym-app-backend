<?php

namespace AppBundle\Controller;

use AppBundle\Exception\UserValidationException;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\Validator\UserValidator;
use FOS\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     * @Route("/api/register", name="user_register", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used to register a user.",
     *  section="Authorization",
     *  filters={
     *      {"name"="email", "dataType"="string", "description" : "Mandatory"},
     *      {"name"="password", "dataType"="string", "description" : "Mandatory"},
     *      {"name"="fullName", "dataType"="string", "description": "Mandatory. Format: last_name first_name"},
     *      {"name"="picture", "dataType"="string", "description" : "Optional"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is not valid"
     *  }
     *  )
     */
    public function registerUserAction(Request $request) : JsonResponse
    {
        $queryParams = $request->query->all();
        $userValidator = $this->get(UserValidator::class);
        try {
            $userValidator->checkMandatoryFields($queryParams);
            $userValidator->validate($queryParams);
        } catch (UserValidationException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        if ($userRepository->findOneBy(['email' => $queryParams['email']]) instanceof User) {
            return new JsonResponse(['error' => 'User with given email already exists!'], 400);
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $this->setUserProperties($user, $queryParams);
        $userManager->updateUser($user);

        return new JsonResponse('', 200);
    }

    /**
     * Returns the highest user role
     *
     * @param User $user
     *
     * @return string
     */
    private function getHighestRole(User $user) : string
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

    /**
     * @param User  $user
     * @param array $data
     */
    private function setUserProperties(User $user, array $data)
    {
        $user->setEmail($data['email']);
        $user->setUsername($user->getEmail());
        $user->setLastName(explode(' ', $data['fullName'])[0]);
        $user->setFirstName(explode(' ', $data['fullName'])[1] ?? '');
        $user->setPicture($data['picture'] ?? '');
        $user->setPlainPassword($data['password']);
        $user->addRole("ROLE_USER");
        $user->setEnabled(true);
    }
}
