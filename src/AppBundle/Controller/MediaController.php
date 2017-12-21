<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helper\FileHelper;
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
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class MediaController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class MediaController extends Controller
{
    const PATH_TO_GYM_PHOTOS = '/uploads/gym-photos';

    /**
     * @var string
     */
    private $webRoot;

    /**
     * MediaController constructor.
     *
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->webRoot = realpath($rootDir . '/../web');
    }
    
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
        $fileHelper = $this->get(FileHelper::class);
        $fileNames = $fileHelper->getGymPhotos();

        return new JsonResponse($fileNames, 200);
    }

    /**
     * @Route("/admin/photos", name="photo_gallery_route")
     */
    public function photoGalleryAction()
    {
        $fileHelper = $this->get(FileHelper::class);
        $fileNames = $fileHelper->getGymPhotos();

        return $this->render(
            'default/gallery.html.twig',
            array('photos' => $fileNames)
        );
    }

    public function getPhotosPath() : string
    {
        return $this->webRoot . self::PATH_TO_GYM_PHOTOS;
    }

    /**
     * @Route("/photos", name="delete_picture", methods={"DELETE"})
     *
     * @param Request $request
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
    public function deletePictureAction(Request $request) : JsonResponse
    {
        $id = $request->get('id');
        if (null === $id) {
            return new JsonResponse(['error' => 'Picture id can\'t be empty/null'], 400);
        }

        $photosPath = $this->getPhotosPath();

        $fileHelper = $this->get(FileHelper::class);
        $fileHelper->removeGalleryPhoto($photosPath, $id);

        return new JsonResponse('', 200);
    }


}
