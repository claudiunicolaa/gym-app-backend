<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class NoteRepository
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class NoteRepository extends EntityRepository
{
    /**
     * Return all the notes that belong to the given user
     *
     * @param User $user
     *
     * @return array
     */
    public function getUserNotes(User $user) : array
    {
        $queryBuilder = $this
            ->createQueryBuilder('n')
            ->select('
                   n.id,
                   n.text,
                   n.creationDate
                   ')
            ->orderBy('n.creationDate', 'ASC')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user);

        $result = [];

        foreach ($queryBuilder->getQuery()->getResult() as $note) {
            $note['creationDate'] = $note['creationDate']->getTimestamp();
            array_push($result,$note);
        }

        return $result;
    }
}
