<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\TeamSlack;
use AppBundle\Entity\UserSlack;
use Doctrine\ORM\EntityRepository;

class MessageSlackRepository extends EntityRepository
{
    public function getChannelMostActiveUsers(ChannelSlack $channel, int $maxResults = 3) {
      $repository = $this->getEntityManager()->getRepository(MessageSlack::class);

      
      $query = $repository->createQueryBuilder('m')
        ->select('count(m)')
        ->addSelect('u')
        ->where('m.channel = :channel')
        ->join(UserSlack::class, 'u', 'WITH', 'm.userSlack = u')
        ->groupBy('m.userSlack')
        ->orderBy('count(m)', 'desc')
        ->setParameter('channel', $channel)
        ->setMaxResults($maxResults)
        ->getQuery();        

        $results = $query->getResult();
        
        
    }
    public function findPinnedMessageByChannel($channel)
    {
        $repository = $this->getEntityManager()->getRepository(MessageSlack::class);
        $query = $repository->createQueryBuilder('m')
            ->where('m.pinned = true')
            ->andWhere('m.channel = :channel')
            ->setParameter('channel', $channel)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();
        $results = $query->getResult();
        return $results;
    }
    public function addDateSearch($query, $date)
    {
        $query->andWhere('q.createdAt LIKE :date')
            ->setParameter('date', '%' . $date . '%');
        return $query;
    }
    public function addContentSearch($query, $content)
    {
        $query->andWhere('q.content LIKE :content')
            ->setParameter('content', '%' . $content . '%');
        return $query;
    }

    public function addChannelSearch($query, $channelId)
    {
        $channel = $this->getEntityManager()->getRepository(ChannelSlack::class)->findOneByIdSlack($channelId);
        if($channel)
        {
            $query->andWhere('q.channel = :channel')
                ->setParameter('channel', $channel);
            return $query;
        }
        return false;
    }
    public function addUserSearch($query, $userId)
    {
        $user = $this->getEntityManager()->getRepository(UserSlack::class)->findOneByIdSlack($userId);
        if($user) {
            $query->andWhere('q.userSlack = :user')
                ->setParameter('user', $user);
            return $query;
        }
        return false;
    }
    public function addTeamSearch($query, $teamId)
    {
        $team = $this->getEntityManager()->getRepository(TeamSlack::class)->findOneByIdSlack($teamId);
        if($team) {
            $query->andWhere('q.team = :team')
                ->setParameter('team', $team);
            return $query;
        }
        return false;
    }

    public function searchInMessages($options, $count = false, $offset = 0, $limit = 50)
    {
        $repository = $this->getEntityManager()->getRepository(MessageSlack::class);
        $query = $repository->createQueryBuilder('q');
        if($count)
        {
            $query->select('count(q.id)');
        }
        foreach($options as $option => $value)
        {
            if($option == 'c')
            {
              $query = $this->addContentSearch($query, $value);
              if(!$query)
              {
                 return false;
              }
            }
            if($option == 'ch')
            {
              $query = $this->addChannelSearch($query, $value);
              if(!$query)
                {
                    return false;
                }
            }
            if($option == 'u')
            {
              $query = $this->addUserSearch($query, $value);
                if(!$query)
                {
                    return false;
                }
            }
            if($option == 'd')
            {
              $query = $this->addDateSearch($query, $value);
                if(!$query)
                {
                    return false;
                }
            }
            if($option == 't')
            {
              $query = $this->addTeamSearch($query, $value);
                if(!$query)
                {
                    return false;
                }
            }
        }

        if($count)
        {
            $results = $query->getQuery()->getSingleScalarResult();
        }
        else {
            $results = $query->orderBy('q.createdAt', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }
        return $results;
    }
}