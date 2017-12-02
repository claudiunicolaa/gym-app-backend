<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function getNoOfUsersAtTheGym() : int
    {
        return $this
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.isAtTheGym = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
