<?php

namespace AppBundle\Security;

use AppBundle\Entity\Course;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
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
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

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
                return $this->canCreate($token);
            case self::UPDATE:
                return $this->canUpdate($course, $token);
            case self::DELETE:
                return $this->canUpdate($course, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canCreate(TokenInterface $token) : bool
    {
        if ($this->decisionManager->decide($token, array('ROLE_TRAINER'))) {
            return true;
        }

        return false;
    }

    /**
     * @param Course $course
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canUpdate(Course $course, TokenInterface $token) : bool
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if ($token->getUser()->getId() === $course->getTrainer()->getId()) {
            return true;
        }

        return false;
    }
}
