<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Exception\UserValidationException;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\Helper\FileHelper;
use AppBundle\Services\Validator\UserValidator;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     *         "email" : "smithadam@gmail.com",
     *         "picture" : "abcdefg.jpg",
     *         "isAtTheGym" : "1"
     *     }
     *
     * @Route("/api/user", name="user_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get the current user",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getUserAction() : JsonResponse
    {
        return new JsonResponse($this->getUser()->toArray(),200);
    }

    /**
     * @Route("/api/user", name="user_update", methods={"PUT"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update the current user. Send it as a POST request (and see mandatory parameters).",
     *  section="User",
     *  filters={
     *      {"name"="fullName", "dataType"="string"},
     *      {"name"="password", "dataType"="string"},
     *      {"name"="picture", "dataType"="File"},
     *      {"name"="isAtTheGym", "dataType"="boolean"},
     *      {"name"="_method", "dataType"="string", "description"="Mandatory: value = PUT"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed",
     *  }
     *  )
     */
    public function updateUserAction(Request $request) : JsonResponse
    {
        $requestParams = $request->request->all();
        unset($requestParams['_method']);
        if (null !== $request->files->get('picture')) {
            $requestParams['picture'] = $request->files->get('picture');
        }

        $userValidator = $this->get(UserValidator::class);
        try {
            $userValidator->validate($requestParams, 'update');
        } catch (UserValidationException $userValidationException) {
            return new JsonResponse(['error' => $userValidationException->getMessage()], 400);
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        $fileHelper = $this->get(FileHelper::class);
        $loggedUser = $this->getUser();

        if (isset($requestParams['picture'])) {
            $fileHelper->removePicture($loggedUser);
            $requestParams['picture'] = $fileHelper->uploadFile($requestParams['picture'], 'user');
        }

        $loggedUser->updateProperties($requestParams);
        $userManager->updateUser($loggedUser);

        return new JsonResponse('', 200);
    }

    /**
     * ### Example Response ###
     *      {
     *          "numberOfUsers": 2
     *      }
     *
     * @Route("/api/users/at-the-gym", name="users_at_the_gym", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns the number of users currently at the gym",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed",
     *  }
     *  )
     */
    public function usersAtTheGymAction() : JsonResponse
    {
        return new JsonResponse(
            ['numberOfUsers' => $this->get(UserRepository::class)->getNoOfUsersAtTheGym()],
            200
        );
    }
}
