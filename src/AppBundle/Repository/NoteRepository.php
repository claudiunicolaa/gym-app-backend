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
                   unix_timestamp(n.creationDate) as creationDate
                   ')
            ->orderBy('n.creationDate', 'ASC')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }
}
