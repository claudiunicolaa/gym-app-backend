<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Course;
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
                   u.picturePath,
                   c.eventDate,
                   c.capacity,
                   c.imagePath,
                   c.name,
                   COUNT(r.id) As registered_users'
            )
            ->innerJoin('c.trainer', 'u')
            ->leftJoin('c.registeredUsers', 'r')
            ->groupBy('c.id')
            ->orderBy('c.eventDate', 'ASC')
        ;

        $this->applyFilters($queryBuilder, $user, $filters);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $loggedUser
     * @param int  $courseId
     *
     * @return int
     */
    public function isRegistered(User $loggedUser, int $courseId) : int
    {
        $usersCourses = $this
            ->createQueryBuilder('c')
            ->select('c.id')
            ->where(':user IN c.registeredUsers')
            ->setParameter('user', $loggedUser)
            ->getQuery()
            ->getResult();

        return (in_array($courseId, $usersCourses)) ? 1 : 0;
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
        if (isset($filters['usersCourses'])) {
            $op = $filters['usersCourses'] === 'true' ? 'MEMBER OF' : 'NOT MEMBER OF';
            $queryBuilder->andWhere(':loggedUser ' . $op . ' c.registeredUsers')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['ownedCourses'])) {
            $op = $filters['ownedCourses'] === 'true' ? '=' : '!=';
            $queryBuilder->andWhere('c.trainer ' . $op . ' :loggedUser')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['intervalStart'])) {
            $date = (new \DateTime())->setTimestamp((int)$filters['intervalStart']);
            $queryBuilder->andWhere('c.eventDate >= :intervalStart')
                ->setParameter('intervalStart', $date)
            ;
        }

        if (isset($filters['intervalStop'])) {
            $date = (new \DateTime())->setTimestamp((int)$filters['intervalStop']);
            $queryBuilder->andWhere('c.eventDate <= :intervalStop')
                ->setParameter('intervalStop', $date)
            ;
        }
    }
}
