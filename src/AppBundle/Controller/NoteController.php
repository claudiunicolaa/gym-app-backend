<?php
/**
 * Created by PhpStorm.
 * User: andu
 * Date: 27.11.2017
 * Time: 00:51
 */

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
 * Class UserController
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class NoteController extends Controller
{

    /**
     * ### Example Response ###
     *      {
     *          {
     *              "id" : "1",
     *              "text" : "Take 1 more course",
     *              "creationDate" : "1508916731"
     *          },
     *          {
     *              "id" : "2",
     *              "text" : "Exercise more",
     *              "creationDate" : "1508916731"
     *          }
     *      }
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
        try {
            $notes = $this
                ->get(NoteRepository::class)
                ->getUserNotes(
                    $this->getUser()
                )
            ;

            return new JsonResponse($this->formatResult($notes), 200);
        } catch (NoteRepositoryException $ex) {
            return new JsonResponse(['error' => $ex->getMessage()], 400);
        }
    }

    /**
     * ### Example Response ###
     *      {
     *          "id" : "1",
     *          "text" : "Take 1 more course",
     *          "creationDate" : "1508916731"
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
     *  description="Returns the note with the given id",
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

        $result = $note->toArray();

        return new JsonResponse($result, 200);
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

        $note = new Note();
        $note->setUser($loggedUser);
        $note->setText($request->get('text'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($note);
        $em->flush();

        return new JsonResponse('', 200);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function formatResult(array $data) : array
    {
        $result = [];

        foreach ($data as $key => $noteData) {
            $result[$key]['creationDate'] = $noteData['creationDate']->getTimestamp();
            $result[$key]['id'] = $noteData['id'];
            $result[$key]['text'] = $noteData['text'];
        }

        return $result;
    }

}