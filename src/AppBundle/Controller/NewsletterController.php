<?php
/**
 * Created by PhpStorm.
 * User: andu
 * Date: 27.11.2017
 * Time: 16:43
 */

namespace AppBundle\Controller;

use AppBundle\Exception\CourseRepositoryException;
use AppBundle\Repository\CourseRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NewsletterController
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
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
        //$form = $this->createForm(NewsletterType::class);
        $form = $this->createFormBuilder()
            ->add('text', TextareaType::class)
            ->add('send',SubmitType::class, array('label' => 'Send newsletter'))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $emailsArray = $this
                ->get(UserRepository::class)
                ->getSubscribedUsers();

            $recipients = "";

            foreach ($emailsArray as $email) {
                $recipients = $recipients . $email['email'];
            }

            $message = (new \Swift_Message('Gym App Newsletter'))
                ->setFrom('gymappnewsletter@gmail.com')
                ->setTo('andu.hulkmaniac@gmail.com')
                ->setBody($form->getData());

            var_dump($message->getBody()); die();

            $this->get('mailer')->send($message);
        }

        return $this->render('default/newsletter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}