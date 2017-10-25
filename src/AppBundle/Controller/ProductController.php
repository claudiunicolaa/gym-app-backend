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
     * @Route("/api/products", name="products_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns the list of products.",
     *  section="Product",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getProductsAction() : JsonResponse
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get(ProductRepository::class);

        return new JsonResponse($productRepository->findAll(), 200);
    }

    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "price" : "45",
     *              "description" : "Example description #1",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "name" : "Example Product B"
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
     *  description="Returns a product with given id.",
     *  section="Product",
     * filters={
     *      {"name"="id", "dataType"="int", "description"="Returns the product with the given id. Mandatory"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing"
     *  }
     *  )
     */
    public function getProductAction(Request $request) : JsonResponse
    {
        $productId = $request->get('id');
        if ($productId === null) {
            return new JsonResponse(['error' => 'Missing required parameter id'], 400);
        }

        /** @var ProductRepository $productRepository */
        $productRepository = $this->get(ProductRepository::class);

        return new JsonResponse($productRepository->find($productId), 200);
    }
}
