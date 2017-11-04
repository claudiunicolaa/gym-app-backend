<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;

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
     *              "name" : "Example Product B",
     *              "category" : "Category A"
     *         },
     *         {
     *              "id" : "2",
     *              "price" : "23.6",
     *              "description" : "Example description #2",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "name" : "Example Product A",
     *              "category" : "Category A"
     *         }
     *     }
     *
     * @Route("/api/products", name="products_get", methods={"GET"})
     *
     * @return Response
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns the list of products.",
     *  section="Product",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getProductsAction() : Response
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get(ProductRepository::class);

        return new Response(
            $this->get('serializer')->serialize(
                $productRepository->findAll(),
                'json'
            ),
            200
        );
    }

    /**
     * ### Example Response ###
     *      {
     *         {
     *              "id" : "1",
     *              "price" : "45",
     *              "description" : "Example description #1",
     *              "image" : "https://i.imgur.com/NiCqGa3.jpg",
     *              "name" : "Example Product B",
     *              "category" : "Category A"
     *         }
     *     }
     *
     * @Route("/api/product/{id}", name="product_get", methods={"GET"})
     *
     * @param Product $product
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a product with given id.",
     *  section="Product",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getProductAction(?Product $product) : JsonResponse
    {
        if (null === $product) {
            return new JsonResponse(['error' => 'Invalid product id'], 400);
        }

        return new JsonResponse($product->toArray(), 200);
    }
}
