<?php

namespace AppBundle\Security;

use AppBundle\Entity\Course;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CourseVoter
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseVoter extends Voter
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::CREATE, self::UPDATE, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Course) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Course $course */
        $course = $subject;
        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($user);
            case self::UPDATE:
                return $this->canUpdate($course, $user);
            case self::DELETE:
                return $this->canUpdate($course, $user);
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function canCreate(User $user) : bool
    {
        $userRoles = $user->getRoles();
        if (!in_array('ROLE_TRAINER', $userRoles) && !in_array('ROLE_ADMIN', $userRoles)) {
            return false;
        }

        return true;
    }

    /**
     * @param Course $course
     * @param User $user
     *
     * @return bool
     */
    private function canUpdate(Course $course, User $user) : bool
    {
        $userRoles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $userRoles) &&
            $user->getId() !== $course->getTrainer()->getId()
        ) {
            return false;
        }

        return true;
    }
}
