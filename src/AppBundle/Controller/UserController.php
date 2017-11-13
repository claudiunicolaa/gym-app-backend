<?php

namespace AppBundle\Controller;

use AppBundle\Exception\UserValidationException;
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
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
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
     * @Route("/api/user", name="user_update", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used to update information about the current user. Request body must be x-www-form-urlencoded.",
     *  section="User",
     *  filters={
     *      {"name"="fullName", "dataType"="string"},
     *      {"name"="picture", "dataType"="string"},
     *      {"name"="password", "dataType"="string"},
     *      {"name"="picture", "dataType"="File"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function updateUserAction(Request $request) : JsonResponse
    {
        $requestParams = $request->request->all();
        $requestParams['picture'] = $request->files->get('picture');

        $userValidator = $this->get(UserValidator::class);
        try {
            $userValidator->validate($requestParams);
        } catch (UserValidationException $userValidationException) {
            return new JsonResponse(['error' => $userValidationException->getMessage()], 400);
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        $fileHelper = $this->get(FileHelper::class);
        $loggedUser = $this->getUser();

        $fileHelper->removePicture($loggedUser);
        $requestParams['picture'] = $fileHelper->uploadFile($request->files->get('picture'), 'user');
        $loggedUser->updateProperties($requestParams);

        $userManager->updateUser($loggedUser);

        return new JsonResponse('', 200);
    }
}
