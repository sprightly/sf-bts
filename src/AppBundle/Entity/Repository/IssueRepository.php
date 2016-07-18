<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{
    public function findAllOpenIssueWhereUserCollaborator(User $user)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.collaborators', 'c')
            ->where("c.id = :user_id AND i.status IN ('open', 'reopen')")
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllOpenIssueWhereUserAssignee(User $user)
    {
        return $this->createQueryBuilder('i')
            ->where("i.assignee = :user_id AND i.status IN ('open', 'reopen')")
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
