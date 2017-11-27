<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
     *         "picture" : "abcdefg.jpg"
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
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        return new JsonResponse(
            [
                'id' => $loggedUser->getId(),
                'fullName' => $loggedUser->getFullName(),
                'email' => $loggedUser->getEmail(),
                'picture' => $loggedUser->getPicturePath()
            ],
            200
        );
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
     * @Route("/api/newsletter/subscription", name="newsletter_subscribe", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to subscribe to the newsletter.",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid.",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function subscribeAction() : JsonResponse
    {
        $loggedUser = $this->getUser();

        $loggedUser->setIsSubscribed(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($loggedUser);
        $em->flush();

        return new JsonResponse('', 200);
    }

    /**
     * @Route("/api/newsletter/subscription", name="newsletter_unsubscribe", methods={"DELETE"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to unsubscribe from the newsletter",
     *  section="User",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function unsubscribeAction() : JsonResponse
    {
        $loggedUser = $this->getUser();

        $loggedUser->setIsSubscribed(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($loggedUser);
        $em->flush();

        return new JsonResponse('', 200);
    }
}
