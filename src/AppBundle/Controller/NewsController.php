<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class NewsController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class NewsController extends Controller
{
    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "text" : "a"
     *         },
     *         {
     *              "id" : "2",
     *              "text" : "b"
     *         }
     *     }
     *
     * @Route("/api/news", name="news_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get all news",
     *  section="News",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getNewsAction() : JsonResponse
    {
        $news = array_map(
            function (News $news) {
                return $news->toArray();
            },
            $this->get(NewsRepository::class)->findAll()
        );

        return new JsonResponse($news, 200);
    }
}
