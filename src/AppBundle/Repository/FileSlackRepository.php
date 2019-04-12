<?php
namespace AppBundle\Repository;

use AppBundle\Entity\FileSlack;
use Doctrine\ORM\EntityRepository;

class FileSlackRepository extends EntityRepository
{
    public function findFilesByChannel($channel)
    {

        $repository = $this->getEntityManager()->getRepository(FileSlack::class);
        $query = $repository->createQueryBuilder('f')
            ->where('f.channel = :channel')
            ->setParameter('channel', $channel)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery();
        $results = $query->getResult();
        return $results;
    }
}