<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class MediaController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class MediaController extends Controller
{
    const PATH_TO_GYM_PHOTOS = '/../web/uploads/gym-photos';
    
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
        $photoPath = $this->getParameter('kernel.root_dir') . self::PATH_TO_GYM_PHOTOS;
        $fileNames = [];

        if (file_exists($photoPath) && is_dir($photoPath)) {
            $finder = new Finder();
            $finder->files()->in($photoPath);
            foreach ($finder as $file) {
                $fileNames[] = $file->getBasename();
            }
        }

        return new JsonResponse($fileNames, 200);
    }

    /**
     * @Route("/admin/photos", name="photo_gallery_route")
     */
    public function homepageAction()
    {
        $photoPath = $this->getParameter('kernel.root_dir') . self::PATH_TO_GYM_PHOTOS;
        $fileNames = [];

        if (file_exists($photoPath) && is_dir($photoPath)) {
            $finder = new Finder();
            $finder->files()->in($photoPath);
            foreach ($finder as $file) {
                $fileNames[] = $file->getBasename();
            }
        }

        return $this->render(
            'default/gallery.html.twig',
            array('photos' => $fileNames)
        );
    }
}
