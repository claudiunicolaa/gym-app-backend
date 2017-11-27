<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Exception\NoteRepositoryException;
use AppBundle\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class NoteController
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class NoteController extends Controller
{
    /**
     * ### Example Response ###
     *      [
     *          {
     *              "id": 1,
     *              "text": "Do more push-ups!",
     *              "creationDate": {
     *                  "date": "2017-11-27 01:41:03.000000",
     *                  "timezone_type": 3,
     *                  "timezone": "Europe/Helsinki"
     *              }
     *          },
     *          {
     *              "id": 2,
     *              "text": "Another note",
     *              "creationDate": {
     *                  "date": "2017-11-27 19:45:26.000000",
     *                  "timezone_type": 3,
     *                  "timezone": "Europe/Helsinki"
     *              }
     *          },
     *          {
     *              "id": 3,
     *              "text": "Yet Another note",
     *              "creationDate": {
     *                  "date": "2017-11-27 21:00:50.000000",
     *                  "timezone_type": 3,
     *                  "timezone": "Europe/Helsinki"
     *              }
     *          }
     *      ]
     *
     * @Route("/api/user/notes", name="notes_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get the current user's notes",
     *  section="Note",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getNotesAction() : JsonResponse
    {
        $notes = $this
            ->get(NoteRepository::class)
            ->getUserNotes(
                $this->getUser()
            );

        return new JsonResponse($notes, 200);
    }

    /**
     * ### Example Response ###
     *      {
     *          "id": 2,
     *          "user": {
     *              "id": 5,
     *              "fullName": "John Doe",
     *              "email": "j.doe@yahoo.com",
     *              "picturePath": "aHs8sJD0.jpg"
     *          },
     *          "creationDate": 1511739663,
     *          "text": "Do more push-ups!"
     *      }
     *
     * @Route("/api/user/note/{id}", name="note_get", methods={"GET"})
     *
     * @param Note $note
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns the note with the given id for the current user",
     *  section="Note",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getNoteAction(?Note $note) : JsonResponse
    {
        if (null === $note) {
            return new JsonResponse(['error' => 'Note with given id doesn\'t exist'], 400);
        }

        return new JsonResponse($note->toArray(), 200);
    }

    /**
     * @Route("/api/user/note", name="note_creation", methods={"POST"})
     *
     * @param Request  $request
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Used when a user wants to create a note",
     *  section="Note",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid.",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function addNoteAction(?Request $request) : JsonResponse
    {
        $loggedUser = $this->getUser();
        $requestParams = $request->request->all();

        if(count($requestParams) > 1) {
            return new JsonResponse(['error' => 'Too many parameters given!'], 400);
        }

        if (!isset($requestParams['text'])) {
            return new JsonResponse(['error' => 'Note has to have text!'], 400);
        }

        if ($requestParams['text'] === "") {
            return new JsonResponse(['error' => 'Note text can\'t be empty!'], 400);
        }

        $note = (new Note())->setUser($loggedUser)->setText($request->get('text'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($note);
        $em->flush();

        return new JsonResponse('', 200);
    }

}