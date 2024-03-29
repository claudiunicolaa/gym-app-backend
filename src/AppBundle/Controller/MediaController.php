<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\PhotoType;
use AppBundle\Services\Helper\FileHelper;
use AppBundle\Validator\Constraints\ImageExtension;
use AppBundle\Validator\Constraints\ImageExtensionValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class MediaController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class MediaController extends Controller
{
    /**
     * ### Example Response ###
     *      [
     *          "dasDASK.jpg",
     *          "megusta.jpg",
     *          "SDUHdhfsk.JPG",
     *          "fhds6ads.jpg",
     *          "JDidsajJ.jpg"
     *      ]
     *
     * @Route("/api/media/photos", name="media_photos", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get all gym photos (/uploads/gym-photos/name-of-photo-with-extension)",
     *  section="Media",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the credentials are invalid",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getPhotosAction(): JsonResponse
    {
        return new JsonResponse($this->get(FileHelper::class)->getGymPhotos(), 200);
    }

    /**
     * @Route("/admin/photos", name="photo_gallery_route")
     * @return Response
     */
    public function photoGalleryAction(): Response
    {
        return $this->render(
            'default/gallery.html.twig',
            array('photos' => $this->get(FileHelper::class)->getGymPhotos())
        );
    }

    /**
     * @Route("/media/photos/{id}", name="delete_picture", methods={"DELETE"})
     *
     * @param string $id
     * @return JsonResponse
     * @ApiDoc(
     *  resource=true,
     *  description="Delete the picture with the given ID",
     *  section="Media",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *  }
     *  )
     */
    public function deletePictureAction(string $id): JsonResponse
    {
        try {
            $this->get(FileHelper::class)->removeGalleryPhoto($id);

            return new JsonResponse('', 200);
        } catch (FileNotFoundException|IOException $ex) {
            return new JsonResponse(['error' => 'Picture can\'t be removed!'], 400);
        }
    }

    /**
     * @Route("/media/photo", name="picture_form")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newPictureAction(Request $request): Response
    {

        $file = $request->files->get('photo');
        $fileHelper = $this->get(FileHelper::class);
        try {
            $fileHelper->validateImage($file);
        } catch (Exception $exception) {
            return $this->redirectToRoute('photo_gallery_route');
        }
        $fileHelper->uploadFile($file, "gym-photos");

        return $this->redirectToRoute('photo_gallery_route');
    }
}
