<?php
namespace AppBundle\Service;
use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer as JMS;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\MessageAnswerSlack;
use AppBundle\Entity\UserSlack;
use AppBundle\Entity\FileSlack;

class MessageManager
{
    private $em;
    private $reactionService;
    private $fileManager;
    private $routeApi;

    public function __construct(EntityManagerInterface $em, ReactionList $reactionService, FileManager $fileManager, RoutesApi $routes, FileManager $files)
    {
        $this->em = $em;
        $this->reactionService = $reactionService;
        $this->fileManager = $fileManager;
        $this->routeApi = $routes;
        $this->fileManager = $files;
    }

    public function searchMessages(Request $request)
    {
        $params = $request->query->all();
        $options = [];
        $expectedKeys = ['ch', 'c', 'd', 'u', 't'];
        foreach($expectedKeys as $key)
        {
            if(key_exists($key, $params))
            {
                $options[$key] = $params[$key];
            }
        }
        if(count($options) <= 0)
        {
            return false;
        }
        $offset = 0;
        $limit = 50;
        $page = 1;
        $countMessages = $this->em->getRepository(MessageSlack::class)->searchInMessages($options, true);
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


        $searchResult = $this->em->getRepository(MessageSlack::class)->searchInMessages($options, false, $offset, $limit);
        if(!$searchResult)
        {
            return false;
        }
        $data = [
            'messages' => array_reverse($searchResult),
            'page' => $page,
            'total_page' => $totalPages,
            'total_result' => $countMessages,

        ];

        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($data, 'json', JMS\SerializationContext::create()->setGroups(['Default', 'search_result']));

        return $data;
    }

    public function addMessage($message, $team = null, $init = false)
    {
        $new = false;
        if(!$init)
        {
            $team = $this->em->getRepository(TeamSlack::class)->findOneByIdSlack($message->team_id);
            $message = $message->event;
            $channel = $this->em->getRepository(ChannelSlack::class)->findOneByIdSlack($message->channel);

        }
        $newMessage = $this->em->getRepository(MessageSlack::class)->findOneByTs($message->ts);
        if(!$newMessage)
        {
            $newMessage = new MessageSlack();
            $newMessage->setTeam($team);
            if (property_exists($message, 'user')) {
                $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($message->user);
                $newMessage->setUserSlack($user);
            }
            if (property_exists($message, 'ts')) {
                $newMessage->setCreatedAt($message->ts)
                    ->setTs($message->ts);
            }
            if(property_exists($message, 'subtype'))
            {
                $newMessage->setSubtype($message->subtype);
                if($message->subtype === 'file_share' && !$init)
                {
                    $file = $this->fileManager->addFile($message->file, $channel);
                    $newMessage->setFile($file);
                }
            }
            $new = true;
        }
        if(!$init)
        {
            $newMessage->setChannel($channel);
        }

        if(property_exists($message, 'pinned_to'))
        {
            if(!$newMessage->getPinned())
            {
                $newMessage->setPinned(true);
            }
        }


        if (property_exists($message, 'text')) {
            if($newMessage->getContent() !== $message->text)
            {
                $newMessage->setContent($message->text);
            }
        }

         if (property_exists($message, 'reactions')) {
             $newMessage = $this->reactionService->addReactions($newMessage, $message->reactions, $init);
         }

        if(property_exists($message, 'file') && $init)
        {
            $file = $this->em->getRepository(FileSlack::class)->findOneByIdSlack($message->file->id);
            if($newMessage->getFile() !== $file)
            {
                $newMessage->setFile($file);
            }
        }
        if($new)
        {
            $this->em->persist($newMessage);
            return $newMessage;
        }
        return true;
    }

    public function addAnswer($answerInfo, $init = false)
    {
        if(!$init)
        {
            $answerInfo = $answerInfo->event;
        }
        $answer = $this->em->getRepository(MessageAnswerSlack::class)->findOneByTs($answerInfo->ts);
        if(!$answer)
        {
            $answer = new MessageAnswerSlack();
            $answer->setThreadTs($answerInfo->thread_ts)
                ->setCreatedAt($answerInfo->ts)
                ->setTs($answerInfo->ts);
        }
        if($answer->getContent() !== $answerInfo->text)
        {
            $answer->setContent($answerInfo->text);
        }

        if(!$init)
        {
            $message = $this->em->getRepository(MessageSlack::class)->findOneByTs($answerInfo->thread_ts);
            $answer->setMessage($message);
        }
        if (property_exists($answerInfo, 'user')) {
            $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($answerInfo->user);
            $answer->setAuthor($user);
        }
        if (property_exists($answerInfo, 'reactions')) {
            $answer = $this->reactionService->addReactions($answer, $answerInfo->reactions, $init);
        }
        $this->em->persist($answer);
    }

    public function deleteMessage($content)
    {
        $message = $this->em->getRepository(MessageSlack::class)->findOneByTs($content->event->deleted_ts);
        if($message)
        {
            $this->em->remove($message);
            $this->em->flush();
            return true;
        }
        $answer = $this->em->getRepository(MessageAnswerSlack::class)->findOneByTs($content->event->deleted_ts);
        if($answer)
        {
            $this->em->remove($answer);
            $this->em->flush();
            return true;
        }
        return false;

    }

    public function pinMessage($content)
    {
        $message = $this->em->getRepository(MessageSlack::class)->findOneByTs($content->event->item->message->ts);
        if($message)
        {
            $message->setPinned(true);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function unPinMessage($content)
    {
        $message = $this->em->getRepository(MessageSlack::class)->findOneByTs($content->event->item->message->ts);
        if($message)
        {
            $message->setPinned(false);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function editMessage($content)
    {
        $message = $this->em->getRepository(MessageSlack::class)->findOneByTs($content->event->message->ts);
        if($message)
        {
            $message->setContent($content->event->message->text);
            $this->em->flush();
            return true;
        }
        $answer = $this->em->getRepository(MessageAnswerSlack::class)->findOneByTs($content->event->message->ts);
        if($answer)
        {
            $answer->setContent($content->event->message->text);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function importMessagesFromSlack($token, $team)
    {

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $channels = $this->em->getRepository(ChannelSlack::class)->findByTeam($team);
        foreach ($channels as $channel)
        {
            $hasMore = true;
            $latest = 0;
            while($hasMore) {
                $url = $this->routeApi->getRouteWithParams('CHANNELS_HISTORY', [
                    'token' => $token,
                    'channel' => $channel->getIdSlack(),
                    'latest' => $latest,
                    'count' => '1000'
                ]);

                $messages = json_decode(file_get_contents($url));

                if (!$messages->ok) {
                    return false;
                }
                foreach($messages->messages as $message){
                    if($message->type === "message")
                    {
                        if(property_exists($message, 'thread_ts') && property_exists($message, 'parent_user_id'))
                        {
                            $this->addAnswer($message, true);
                        }
                        else {
                            $newMessage = $this->addMessage($message,  $team, true);
                            if($newMessage instanceof MessageSlack)
                            {
                                $channel->addMessage($newMessage);
                            }

                        }
                    }
                }

                $hasMore = $messages->has_more;
                if($hasMore)
                {
                    $latest = $messages->messages['999']->ts;
                }
            }
        }

        $this->em->flush();
        $replies = $this->em->getRepository(MessageAnswerSlack::class)->findAll();
        foreach ($replies as $reply)
        {
            $thread = $this->em->getRepository(MessageSlack::class)->findOneByTs($reply->getThreadTs());
            $reply->setMessage($thread);
        }

        $this->em->flush();

        return true;
    }
}
