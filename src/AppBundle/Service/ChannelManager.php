<?php
namespace AppBundle\Service;

use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer as JMS;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\FileSlack;
use AppBundle\Entity\UserSlack;

class ChannelManager
{
    private $em;
    private $routeApi;

    public function __construct(EntityManagerInterface $em, RoutesApi $route)
    {
        $this->em = $em;
        $this->routeApi = $route;
    }
    public function getChannelList(TeamSlack $team)
    {
        $channels = $this->em->getRepository(ChannelSlack::class)->findBy(
            ["team" => $team]
        );
        if(!$channels)
        {
            return false;
        }
        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($channels, 'json', JMS\SerializationContext::create()->setGroups(['Default', 'channelList']));
        return $data;
    }
    public function getChannelFullDetailsWithPage(ChannelSlack $channel, Request $request = null)
    {
        $offset = 0;
        $limit = 50;
        $page = 1;
        $countMessages = count($channel->getMessages());
        $totalPages = ceil($countMessages/ $limit);

        if($request != null)
        {
            $page = $request->query->get('page', 1);

            if($page !== false)
            {
                $page = (int)$page;
                if($page > $totalPages)
                {
                    $page = $totalPages;
                }
                if($page <= 0)
                {
                    $page = 1;
                }

                $offset = $limit * ($page - 1);
            }
        }

        $messages = $this->em->getRepository(MessageSlack::class)->findBy(
            ['channel' => $channel],
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );

        $data = [
            'messages' => array_reverse($messages),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages ?: false,
        ];

        if($page == 1)
        {
            $pinnedItems = [
                'messages' =>  $this->em->getRepository(MessageSlack::class)->findPinnedMessageByChannel($channel),
                'files' => $this->em->getRepository(FileSlack::class)->findBy(
                    ['channel' => $channel, 'isPinned' => true]
                )
            ];
            $files = $this->em->getRepository(FileSlack::class)->findFilesByChannel($channel);
            $data['channel'] = $channel;
            $data['pinned_items'] = $pinnedItems;
            $data['files'] = $files;
        }



        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($data, 'json', JMS\SerializationContext::create()->setGroups(['Default','details']));


       return $data;
    }
    public function addChannel($channel, $team, $init = false)
    {
        $new = false;
        if(!$init)
        {
            $channel = $channel->event->channel;
        }
        $newChannel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($channel->id);
        if(!$newChannel)
        {
            $newChannel = new ChannelSlack();
            $newChannel->setIdSlack($channel->id)
                ->setTeam($team);
            $new = true;
        }
        $newChannel->setName($channel->name)
            ->setCreatedAt($channel->created);

        if(property_exists($channel, 'purpose'))
        {
            $newChannel->setPurpose($channel->purpose->value);
        }
        else{
            $newChannel->setPurpose('none');
        }


        $creator = $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($channel->creator);
        $newChannel->setCreator($creator);
        foreach($channel->members as $member)
        {
            $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($member);
            if(!$newChannel->getUsers()->contains($user))
            {
                $newChannel->addUser($user);
            }
        }
        if($new)
        {
            $this->em->persist($newChannel);
        }
    }
    public function removeChannel($content)
    {
        $channel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($content->event->channel);
        if($channel)
        {
            $this->em->remove($channel);
            $this->em->flush();
            return true;
        }
        return false;
    }
    public function renameChannel($content)
    {
        $channel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($content->event->channel->id);
        if($channel)
        {
            $channel->setName($content->event->channel->name);
            $this->em->flush();
            return true;
        }
        return false;

    }
    public function importChannelsFromSlack($team)
    {
       $route = $this->routeApi->getRouteWithParams('CHANNELS_LIST', ['token' => $team->getToken()]);

       $channelList = json_decode(file_get_contents($route));
       if($channelList->ok)
       {

           foreach($channelList->channels as $channel)
           {
               $this->addChannel($channel, $team, true);

           }
           $this->em->flush();

           return true;
       }
       return false;
    }
}