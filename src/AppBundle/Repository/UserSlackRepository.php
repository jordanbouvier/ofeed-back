<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\UserSlack;
use Doctrine\ORM\EntityRepository;

class UserSlackRepository extends EntityRepository
{
    public function findByMessageNumberAndChannel($channel)
    {

    }

}