<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class IssueActivityRepository extends EntityRepository
{
    public function findAllVisibleForCurrentUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.project', 'p')
            ->leftJoin('p.members', 'm')
            ->where("m.id = :user_id")
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
