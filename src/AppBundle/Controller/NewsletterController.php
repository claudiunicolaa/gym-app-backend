<?php

namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Form\Type\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class NewsletterController
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/admin/newsletter", name="newsletter_form")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request) : Response
    {
        $form = $this->createForm(NewsletterType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $emailsArray = $this
                ->get(UserRepository::class)
                ->getSubscribedUsers();

            $message = (new \Swift_Message('Gym App Newsletter'))
                ->setFrom('gymappnewsletter@gmail.com')
                ->setTo(array_column($emailsArray,'email'))
                ->setBody($form->get('message')->getData());

            $this->get('mailer')->send($message);

            return $this->render('default/newsletter_success.html.twig');
        }

        return $this->render('default/newsletter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/api/newsletter/subscription", name="newsletter_subscribe", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to subscribe to the newsletter",
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

        $loggedUser->setSubscribed(true);
        $this->getDoctrine()->getManager()->flush();

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

        $loggedUser->setSubscribed(false);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('', 200);
    }
}
