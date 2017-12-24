<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helper\FileHelper;
use Doctrine\DBAL\Schema\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;
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
     *  description="Get gym photos. Go to /uploads/gym-photos/name-of-photo-with-extension",
     *  section="Media",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the credentials are invalid",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getPhotosAction() : JsonResponse
    {
        return new JsonResponse($this->get(FileHelper::class)->getGymPhotos(), 200);
    }

    /**
     * @Route("/admin/photos", name="photo_gallery_route")
     * @return Response
     */
    public function photoGalleryAction() : Response
    {
        return $this->render(
            'default/gallery.html.twig',
            array('photos' => $this->get(FileHelper::class)->getGymPhotos())
        );
    }

    /**
     * @Route("/photos/{id}", name="delete_picture", methods={"DELETE"})
     *
     * @param null|string $id
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
    public function deletePictureAction(?string $id) : JsonResponse
    {
        if (null === $id) {
            return new JsonResponse(['error' => 'Picture id can\'t be empty/null'], 400);
        }

        $this->get(FileHelper::class)->removeGalleryPhoto($id);

        return new JsonResponse('', 200);
    }
}
