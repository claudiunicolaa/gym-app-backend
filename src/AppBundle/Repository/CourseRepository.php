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
     * Returns all the courses in the database that match the given filters
     *
     * @param User  $user
     * @param array $filters
     *
     * @return array
     */
    public function getFilteredCourses(User $user, array $filters = []) : array
    {
        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->select(
            'c.id,
                   u.id As trainer_id,
                   u.email,
                   u.lastName,
                   u.firstName,
                   u.picture,
                   c.eventDate,
                   c.capacity,
                   c.image,
                   c.name,
                   COUNT(r.id) As registered_users'
            )
            ->innerJoin('c.trainer', 'u')
            ->leftJoin('c.registeredUsers', 'r')
            ->groupBy('c.id')
        ;

        $this->applyFilters($queryBuilder, $user, $filters);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder  $queryBuilder
     * @param User          $loggedUser
     * @param array         $filters already validated. Don't call this method without validation
     *
     * @return void
     *
     * @throws CourseRepositoryException if the filters are invalid
     */
    protected function applyFilters(QueryBuilder $queryBuilder, User $loggedUser, array $filters = []) : void
    {
        if (isset($filters['users_courses'])) {
            $op = $filters['users_courses'] === 'true' ? 'MEMBER OF' : 'NOT MEMBER OF';
            $queryBuilder->andWhere(':loggedUser ' . $op . ' c.registeredUsers')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['owned_courses'])) {
            $op = $filters['owned_courses'] === 'true' ? '=' : '!=';
            $queryBuilder->andWhere('c.trainer ' . $op . ' :loggedUser')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['interval_start'])) {
            $date = (new \DateTime())->setTimestamp((int)$filters['interval_start']);
            $queryBuilder->andWhere('c.eventDate >= :interval_start')
                ->setParameter('interval_start', $date)
            ;
        }

        if (isset($filters['interval_stop'])) {
            $date = (new \DateTime())->setTimestamp((int)$filters['interval_stop']);
            $queryBuilder->andWhere('c.eventDate <= :interval_stop')
                ->setParameter('interval_stop', $date)
            ;
        }
    }
}
