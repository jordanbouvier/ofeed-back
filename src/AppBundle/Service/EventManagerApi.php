<?php
namespace AppBundle\Service;

use AppBundle\Entity\FileSlack;
use AppBundle\Entity\MessageAnswerSlack;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\UserSlackManager;

class EventManagerApi
{
    const MESSAGE = "message";
    const MESSAGE_DELETED = "message_deleted";
    const MESSAGE_CHANGED = "message_changed";
    const CHANNEL_CREATED = "channel_created";
    const CHANNEL_DELETED = "channel_deleted";
    const CHANNEL_RENAME = "channel_rename";
    const MEMBER_JOINED_CHANNEL = "member_joined_channel";
    const MEMBER_LEFT_CHANNEL = "member_left_channel";
    const TEAM_JOIN = "team_join";
    const PIN_ADDED = "pin_added";
    const PIN_REMOVED = "pin_removed";
    const REACTION_ADDED = "reaction_added";
    const REACTION_REMOVED = "reaction_removed";

    private $messageManager;
    private $em;
    private $channelManager;
    private $userSlackManager;
    private $reactionService;

    public function __construct(ReactionList $reactionService, MessageManager $messageManager, EntityManagerInterface $em, ChannelManager $channelManager, UserSlackManager $userSlackManager)
    {
        $this->messageManager = $messageManager;
        $this->channelManager = $channelManager;
        $this->userSlackManager = $userSlackManager;
        $this->reactionService = $reactionService;
        $this->em = $em;
    }

    public function dispatchEvent($content)
    {
        $content = json_decode($content);
        if(property_exists($content, 'team_id'))
        {
            $team = $this->em->getRepository(TeamSlack::class)->findOneByIdSlack($content->team_id);
            if($team)
            {
                if(property_exists($content, 'event'))
                {
                    $eventType = $content->event->type;
                    $eventAction = '';
                    if(property_exists($content->event, 'subtype'))
                    {
                        $eventAction = $content->event->subtype;
                    }

                    switch($eventType)
                    {
                        case self::MESSAGE : {
                            switch ($eventAction) {
                                case self::MESSAGE_DELETED:
                                    $result = $this->messageManager->deleteMessage($content);
                                    return $result;
                                case self::MESSAGE_CHANGED:
                                    $result = $this->messageManager->editMessage($content);
                                    return $result;
                                default:
                                    if(property_exists($content->event, 'thread_ts') && property_exists($content->event, 'parent_user_id'))
                                    {
                                        $this->messageManager->addAnswer($content);
                                        $this->em->flush();
                                        return true;
                                    }
                                    $this->messageManager->addMessage($content);
                                    $this->em->flush();
                                    return true;
                            }
                            break;
                        }
                        case self::CHANNEL_CREATED:
                            $this->channelManager->addChannel($content, $team);
                            $this->em->flush();
                            break;

                        case self::CHANNEL_DELETED:
                            $result = $this->channelManager->removeChannel($content);
                            return $result;

                        case self::CHANNEL_RENAME:
                            $result = $this->channelManager->renameChannel($content);
                            return $result;

                        case self::MEMBER_JOINED_CHANNEL:
                            $result = $this->userSlackManager->joinChannel($content);
                            return $result;

                        case self::MEMBER_LEFT_CHANNEL:
                            $result = $this->userSlackManager->leaveChannel($content);
                            return $result;

                        case self::TEAM_JOIN:
                            $result = $this->userSlackManager->addUser($content);
                            $this->em->flush();
                            return $result;

                        case self::PIN_ADDED:
                            if(property_exists($content->event, 'item'))
                            {
                                if($content->event->item->type === "message")
                                {
                                    $this->messageManager->pinMessage($content);
                                    return true;
                                }
                            }
                            break;

                        case self::PIN_REMOVED:
                            if(property_exists($content->event, 'item'))
                            {
                                if($content->event->item->type === "message")
                                {
                                    $this->messageManager->unPinMessage($content);
                                    return true;
                                }
                            }
                            break;
                        case self::REACTION_ADDED:
                            $possibleTargetList = $this->reactionService->getAcceptedType();
                            foreach($possibleTargetList as $target)
                            {
                                $item = $this->em->getRepository($target)->findOneByTs($content->event->item->ts);
                                if($item)
                                {
                                    $this->reactionService->addReactionToTarget($content, $item);
                                    return true;
                                }

                            }
                            return false;

                        case self::REACTION_REMOVED:
                            $possibleTargetList = $this->reactionService->getAcceptedType();
                            foreach($possibleTargetList as $target)
                            {
                                $item = $this->em->getRepository($target)->findOneByTs($content->event->item->ts);
                                if($item)
                                {
                                    $this->reactionService->removeReactionToTarget($content, $item);
                                    return true;
                                }

                            }
                            return false;
                    }
                }
            }
        }
        return false;

    }
}