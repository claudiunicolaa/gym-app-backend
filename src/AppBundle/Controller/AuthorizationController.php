<?php

namespace AppBundle\Controller;

use AppBundle\Exception\UserValidationException;
use AppBundle\Services\Helper\FileHelper;
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
     * @Route("/", name="default_route")
     */
    public function homepageAction()
    {
        return $this->redirectToRoute('sonata_admin_dashboard');
    }

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
     *  description="User login.",
     *  section="Authorization",
     *  filters={
     *      {"name"="email", "dataType"="string"},
     *      {"name"="password", "dataType"="string"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the credentials are invalid",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function loginAction(Request $request) : JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if (!isset($email) || !isset($password)) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        /** @var User $user */
        $user =  $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
                $tokenManager = $this->get('lexik_jwt_authentication.jwt_manager');
                return new JsonResponse(
                    [
                        'token' => $tokenManager->create($user),
                        'role' => $user->getHighestRole()
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
     *  description="User register.",
     *  section="Authorization",
     *  filters={
     *      {"name"="email", "dataType"="string", "description" : "Mandatory"},
     *      {"name"="password", "dataType"="string", "description" : "Mandatory"},
     *      {"name"="fullName", "dataType"="string", "description": "Mandatory. Format: last_name first_name"},
     *      {"name"="picture", "dataType"="File", "description" : "Optional"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is not valid",
     *      405="Returned when the method called is not allowed",
     *      413="Returned if the picture provided is too big. 2MB allowed"
     *  }
     *  )
     */
    public function registerUserAction(Request $request) : JsonResponse
    {
        $requestParams = $request->request->all();
        if (null !== $request->files->get('picture')) {
            $requestParams['picture'] = $request->files->get('picture');
        }

        $userValidator = $this->get(UserValidator::class);
        try {
            $userValidator->checkMandatoryFields($requestParams);
            $userValidator->validate($requestParams, 'create');
        } catch (UserValidationException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        if ($userRepository->findOneBy(['email' => $requestParams['email']]) instanceof User) {
            return new JsonResponse(['error' => 'User with given email already exists!'], 400);
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        $requestParams['picture'] = $this->get(FileHelper::class)->uploadFile($request->files->get('picture'), 'user');
        $user = $userManager->createUser()->setProperties($requestParams);
        $userManager->updateUser($user);

        return new JsonResponse('', 200);
    }
}
