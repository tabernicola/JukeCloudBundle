<?php

namespace Tabernicola\JukeCloudBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArtistRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArtistRepository extends EntityRepository
{
    public function search($q){
        $qb = $this->createQueryBuilder('a');

        $qb->select('a')
            ->where('a.name LIKE :q')
            ->addorderBy('a.name','ASC')
            ->setParameter('q', "%$q%");
        return $qb->getQuery()->getResult();
    }
}
