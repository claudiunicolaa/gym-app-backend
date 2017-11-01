<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Exception\CourseRepositoryException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CourseRepository
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseRepository extends EntityRepository
{
    /**
     * @param User  $user
     * @param array $filters
     *
     * @return array
     */
    public function getCourses(User $user, array $filters = []) : array
    {
        $qb = $this->createQueryBuilder('c');
        $this->applyFilters($qb, $user, $filters);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder  $qb
     * @param User          $loggedUser
     * @param array         $rawFilters
     *
     * @return void
     *
     * @throws CourseRepositoryException if the filters are invalid
     */
    protected function applyFilters(QueryBuilder $qb, User $loggedUser, array $rawFilters = []) : void
    {
        $allowedFilters = ['users_courses', 'owned_courses', 'interval_start', 'interval_stop'];
        $filters = array_intersect_key($rawFilters, array_flip($allowedFilters));

        if (count($filters) !== count($rawFilters)) {
            throw new CourseRepositoryException(['error' => 'Invalid query params given!'], 400);
        }

        if (isset($filters['users_courses'])) {
            if (!is_bool($filters['users_courses'])) {
                throw new CourseRepositoryException('Invalid value for users_courses parameter!');
            }

            $qb->andWhere(':loggedUser MEMBER OF c.registeredUsers')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['owned_courses'])) {
            if (!is_bool($filters['owned_courses'])) {
                throw new CourseRepositoryException('Invalid value for owned_courses parameter!');
            }

            $qb->andWhere('c.trainer = :loggedUser')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['interval_start'])) {
            if (!is_numeric($filters['interval_start'])) {
                throw new CourseRepositoryException('Invalid value for interval_start parameter!');
            }

            $qb->andWhere('c.eventDate >= :interval_start')
                ->setParameter('interval_start', $filters['interval_start'])
            ;
        }

        if (isset($filters['interval_stop'])) {
            if (!is_numeric($filters['interval_stop'])) {
                throw new CourseRepositoryException('Invalid value for interval_stop parameter!');
            }

            $qb->andWhere('c.eventDate <= :interval_stop')
                ->setParameter('interval_stop', $filters['interval_stop'])
            ;
        }
    }
}
