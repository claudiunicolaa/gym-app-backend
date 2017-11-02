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
        $qb = $this
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

        $this->applyFilters($qb, $user, $filters);

        return $this->formatResult($qb->getQuery()->getResult());
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
            throw new CourseRepositoryException('Invalid query params given!');
        }

        if (isset($filters['users_courses'])) {
            $filters['users_courses'] = strtolower($filters['users_courses']);
            if (!in_array($filters['users_courses'], ['true', 'false'])) {
                throw new CourseRepositoryException('Invalid value for users_courses parameter!');
            }

            $op = $filters['users_courses'] === 'true' ? 'MEMBER OF' : 'NOT MEMBER OF';
            $qb->andWhere(':loggedUser ' . $op . ' c.registeredUsers')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['owned_courses'])) {
            $filters['owned_courses'] = strtolower($filters['owned_courses']);
            if (!in_array($filters['owned_courses'], ['true', 'false'])) {
                throw new CourseRepositoryException('Invalid value for owned_courses parameter!');
            }

            $op = $filters['owned_courses'] === 'true' ? '=' : '!=';
            $qb->andWhere('c.trainer ' . $op . ' :loggedUser')
                ->setParameter('loggedUser', $loggedUser)
            ;
        }

        if (isset($filters['interval_start'])) {
            $intervalStart = $filters['interval_start'];
            if (!is_numeric($intervalStart) || (int)$intervalStart > 2554416000 || (int)$intervalStart < 0) {
                throw new CourseRepositoryException('Invalid value for interval_start parameter!');
            }

            $date = (new \DateTime())->setTimestamp((int)$intervalStart);
            $qb->andWhere('c.eventDate >= :interval_start')
                ->setParameter('interval_start', $date)
            ;
        }

        if (isset($filters['interval_stop'])) {
            $intervalStop = $filters['interval_stop'];
            if (!is_numeric($intervalStop) || (int)$intervalStop < 0 || (int)$intervalStop > 2554416000) {
                throw new CourseRepositoryException('Invalid value for interval_stop parameter!');
            }

            $date = (new \DateTime())->setTimestamp((int)$intervalStop);
            $qb->andWhere('c.eventDate <= :interval_stop')
                ->setParameter('interval_stop', $date)
            ;
        }
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function formatResult(array $result) : array
    {
        foreach (array_keys($result) as $key) {
            $result[$key]['trainer'] = [];
            $result[$key]['trainer']['id'] = $result[$key]["trainer_id"];
            $result[$key]['trainer']['fullName'] = $result[$key]['lastName'] . ' ' .$result[$key]['firstName'];
            $result[$key]['trainer']['email'] = $result[$key]['email'];
            $result[$key]['trainer']['picture'] = $result[$key]['picture'];
            $result[$key]['eventDate'] = $result[$key]['eventDate']->getTimestamp();

            unset($result[$key]['trainer_id']);
            unset($result[$key]['lastName']);
            unset($result[$key]['firstName']);
            unset($result[$key]['picture']);
            unset($result[$key]['email']);
        }

        return $result;
    }
}
