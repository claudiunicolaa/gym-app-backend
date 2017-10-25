<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Repository\ProductRepository;

/**
 * Class ProductController
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class ProductController extends Controller
{
    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "price" : "45",
     *              "description" : "Example description #1",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "name" : "Example Product B"
     *         },
     *         {
     *              "id" : "2",
     *              "price" : "23.6",
     *              "description" : "Example description #2",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "name" : "Example Product A"
     *         }
     *     }
     *
     * @Route("/api/product", name="product_get", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a list of products that match the given filters.",
     *  section="Product",
     * filters={
     *      {"name"="id", "dataType"="int", "description"="Returns the product with the given id. Optional"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getProductsAction(Request $request) : JsonResponse
    {
        $productId = $request->get('id');

        /** @var ProductRepository $productRepository */
        $productRepository = $this->get(ProductRepository::class);

        if ($productId !== null) {
            return new JsonResponse($productRepository->find($productId), 200);
        }

        return new JsonResponse($productRepository->findAll(), 200);
    }
}
