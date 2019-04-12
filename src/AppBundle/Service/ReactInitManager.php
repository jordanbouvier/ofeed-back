<?php
namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer as JMS;
use AppBundle\Entity\TeamSlack;
use AppBundle\Entity\ChannelSlack;
class ReactInitManager
{
    private $cm;
    private $em;

    public function __construct(ChannelManager $cm, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cm = $cm;
    }
    public function homePageInit()
    {
        $teams = $this->em->getRepository(TeamSlack::class)->findAll();
        if(!$teams)
        {
            return false;
        }
        $serializer = JMS\SerializerBuilder::create()->build();
        $data = [
            'teams' => $serializer->serialize($teams, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list']))
        ];
        return $data;
    }
    public function teamInit(TeamSlack $team)
    {
        $teams = $this->em->getRepository(TeamSlack::class)->findAll();
        $channels = $this->em->getRepository(ChannelSlack::class)->findBy(
            ['team' => $team]
        );
        if(!$channels || !$teams)
        {
            return false;
        }

        $serializer = JMS\SerializerBuilder::create()->build();
        $data = [
            'teams' => $serializer->serialize($teams, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list'])),
            'channels' =>  $serializer->serialize($channels, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list', 'defaultState'])),
            'team' => $serializer->serialize($team, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list'])),
        ];
        return $data;
    }
    public function channelDetailsInit(ChannelSlack $channel, TeamSlack $team)
    {
        $teams = $this->em->getRepository(TeamSlack::class)->findAll();
        $channels = $this->em->getRepository(ChannelSlack::class)->findBy(
            ['team' => $team]
        );

        if(!$channels || !$teams){
            return false;
        }

        $data['channel'] = $this->cm->getChannelFullDetailsWithPage($channel);
        $serializer = JMS\SerializerBuilder::create()->build();
        $data['channels'] =  $serializer->serialize($channels, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list', 'defaultState']));
        $data['teams'] = $serializer->serialize($teams, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list']));
        $data['team'] = $serializer->serialize($team, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list']));

        return $data;

    }

}