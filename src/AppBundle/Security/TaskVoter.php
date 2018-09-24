<?php

namespace AppBundle\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    const DELETE = 'delete';

    const UPDATE = 'update';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::DELETE, self::UPDATE))) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($task, $user);
            case self::UPDATE:
                return $this->canUpdate($task, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Task $task, User $user)
    {
        if($task->getUser() === $user || $user->isAdmin()) {
            return true;
        }
    }

    private function canUpdate(Task $task, User $user)
    {
        if($task->getUser() === $user || $user->isAdmin()) {
            return true;
        }
    }
}