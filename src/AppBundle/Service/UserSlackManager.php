<?php
namespace AppBundle\Service;
use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\UserSlack;

class UserSlackManager
{
    private $em;
    private $routeApi;

    public function __construct(EntityManagerInterface $em, RoutesApi $routes)
    {
        $this->em = $em;
        $this->routeApi = $routes;
    }
    public function addUser($member, $team = null, $init = false)
    {
        if(!$init){
            $member = $member->event->user;
            $team = $this->em->getRepository(TeamSlack::class)->findOneByIdSlack($member->team_id);
        }
        $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($member->id);
        if(!$user)
        {
            $user = new UserSlack();
        }
        $user->setIdSlack($member->id)
            ->setUpdateDate($member->updated)
            ->setTeam($team)
            ->setPicture($member->profile->image_32);

        if(property_exists($member->profile, 'display_name'))
        {
            $user->setDisplayName($member->profile->display_name);
        }
        else{
            $user->setDisplayName('none');
        }

        if(property_exists($member->profile, 'real_name'))
        {
            $user->setRealName($member->profile->real_name);
        }
        else{
            $user->setDisplayName('none');
        }
        if(property_exists($member->profile, 'email'))
        {
            $user->setEmail($member->profile->email);
        }
        if(property_exists($member->profile,'image_512'))
        {
            $user->setBigPicture($member->profile->image_512);
        }

        $this->em->persist($user);
        return true;
    }
    public function removeUser($content)
    {
        $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($content->event->id);
        if($user)
        {
            $this->em->remove($user);
            $this->em->flush();
            return true;
        }
       return false;
    }
    public function joinChannel($content)
    {
        $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($content->event->user);
        $channel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($content->event->channel);
        if($channel && $user)
        {
            $channel->addUser($user);
            $this->em->flush();
            return true;
        }
        return false;
    }
    public function leaveChannel($content)
    {
        $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($content->event->user);
        $channel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($content->event->channel);
        if($channel && $user)
        {
            $channel->removeUser($user);
            $this->em->flush();
            return true;
        }
        return false;
    }
    public function importUsersFromSlack($token, $team)
    {
        $url = $this->routeApi->getRouteWithParams('USERS_LIST', ['token' => $token]);
        $userList = json_decode(file_get_contents($url));
        if($userList->ok)
        {
            foreach($userList->members as $member)
            {
                $this->addUser($member,$team, true);
            }
            $this->em->flush();

            return true;
        }
        return false;
    }
}