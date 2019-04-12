<?php
namespace AppBundle\Service;

use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class InitWorkspace
{
    private $em;
    private $messages;
    private $channels;
    private $files;
    private $users;
    private $team;
    private $token;
    private $logger;
    private $emojis;

    public function __construct(EmojiManager $emojis, LoggerInterface $logger, TokenGen $tokenGen, TeamInfo $teamInfo, EntityManagerInterface $em, UserSlackManager $userList, ChannelManager $channelList, MessageManager $messageChannel, FileManager $fileList)
    {
        $this->em = $em;
        $this->token = $tokenGen;
        $this->team = $teamInfo;
        $this->users = $userList;
        $this->channels = $channelList;
        $this->messages = $messageChannel;
        $this->files = $fileList;
        $this->logger = $logger;
        $this->emojis = $emojis;
    }


    public function initData($code, $clientId, $clientSecret)
    {

        if($code)
        {
            $info = json_decode($this->token->getToken($clientId,$clientSecret,$code));

            if($info->ok)
            {
                $team = $this->team->getTeamInfo($info->access_token);

                if($team instanceof TeamSlack)
                {
                    $teamExists = $this->em->getRepository(TeamSlack::class)->findOneByIdSlack($team->getIdSlack());
                    if($teamExists)
                    {
                        return "The workspace is already registered";
                    }
                    $transactionStatus = true;
                    $this->em->beginTransaction();
                    $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
                    $this->em->persist($team);
                    $this->em->flush();

                    $this->logger->info("Utilisateurs");
                    $start = microtime(true);
                    $userListResult = $this->users->importUsersFromSlack($team->getToken(), $team);
                    $this->logger->info(microtime(true) - $start);

                    if($userListResult)
                    {
                        $this->logger->info("Channels");
                        $start = microtime(true);
                        $channelListResult = $this->channels->importChannelsFromSlack($team);
                        $this->logger->info(microtime(true) - $start);

                        if ($channelListResult) {
                            $this->logger->info("Fichiers");
                            $start = microtime(true);
                            $filesResult = $this->files->importFilesFromSlack($team);
                            $this->logger->info(microtime(true) - $start);

                            if($filesResult) {
                                $this->logger->info("Messages");
                                $start = microtime(true);
                                $messagesResult = $this->messages->importMessagesFromSlack($team->getToken(), $team);
                                $this->logger->info(microtime(true) - $start);

                                if ($messagesResult) {
                                    $this->emojis->importAllEmojiFromSlack($team);
                                    $this->em->commit();
                                    return true;
                                }
                            }
                        }

                    }
                }
            }
        }
        if(isset($transactionStatus))
        {
            $this->em->rollback();
        }
        return false;
    }

}