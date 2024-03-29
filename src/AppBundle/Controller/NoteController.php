<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Exception\NoteRepositoryException;
use AppBundle\Exception\NoteValidationException;
use AppBundle\Repository\NoteRepository;
use AppBundle\Services\Validator\NoteValidator;
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
     *              "creationDate": "1511739663"
     *          },
     *          {
     *              "id": 2,
     *              "text": "Another note",
     *              "creationDate": "1511804726"
     *          },
     *          {
     *              "id": 3,
     *              "text": "Yet Another note",
     *              "creationDate": "1511809250"
     *          }
     *      ]
     *
     * @Route("/api/user/notes", name="notes_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get all notes",
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
     *          "creationDate": "1511739663",
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
     *  description="Get note by id",
     *  section="Note",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      403="Returned when you are not the owner of the note",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function getNoteAction(?Note $note) : JsonResponse
    {
        if (null === $note) {
            return new JsonResponse(['error' => 'Note with given id doesn\'t exist'], 400);
        }

        if ($note->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'You don\'t own this note!'], 403);
        }

        return new JsonResponse($note->toArray(), 200);
    }

    /**
     * @Route("/api/user/note/{id}", name="note_delete", methods={"DELETE"})
     *
     * @param Note $note
     *
     * @return JsonResponse
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Delete note by id",
     *  section="Note",
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      403="Returned when you are not the owner of the note",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function deleteNoteAction(?Note $note) : JsonResponse
    {
        if (null === $note) {
            return new JsonResponse(['error' => 'Note with given id doesn\'t exist'], 400);
        }

        if ($note->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'You don\'t own this note!'], 403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($note);
        $em->flush();

        return new JsonResponse('', 200);
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
     *  description="Create a note",
     *  section="Note",
     *  filters={
     *      {"name"="text", "dataType"="string", "description" : "Mandatory"},
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the request is invalid.",
     *      401="Returned when the request is valid, but the token given is invalid or missing",
     *      405="Returned when the method called is not allowed"
     *  }
     *  )
     */
    public function createNoteAction(Request $request) : JsonResponse
    {
        $loggedUser = $this->getUser();
        $requestParams = $request->request->all();
        $noteValidator = $this->get(NoteValidator::class);
        try {
            $noteValidator->checkMandatoryFields($requestParams);
            $noteValidator->validate($requestParams);
            $note = (new Note())->setUser($loggedUser)->setText($request->get('text'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            return new JsonResponse($note->toArray(), 200);
        } catch (NoteValidationException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }
    }
}
