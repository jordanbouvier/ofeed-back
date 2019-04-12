<?php
namespace AppBundle\Service;

use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateData
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


    public function update()
    {
        $teams = $this->em->getRepository(TeamSlack::class)->findAll();

        if($teams)
        {
            foreach ($teams as $team)
            {
                $this->em->beginTransaction();
                $start = microtime(true);
                $userListResult = $this->users->importUsersFromSlack($team->getToken(), $team);

                if($userListResult)
                {
                    dump('Update users ' .  $team->getName() . ' in ' . (number_format(microtime(true) - $start, 8)) . 'sc');

                    $start = microtime(true);
                    $channelListResult = $this->channels->importChannelsFromSlack($team);
                    $this->logger->info(microtime(true) - $start);

                    if ($channelListResult) {
                        dump('Update channels ' .  $team->getName() . ' in ' . (number_format(microtime(true) - $start, 8)) . 'sc');
                        $start = microtime(true);
                        $filesResult = $this->files->importFilesFromSlack($team);
                        $this->logger->info(microtime(true) - $start);

                        if($filesResult) {
                            dump('Update files ' . $team->getName() . ' in ' . (number_format(microtime(true) - $start, 8)) . 'sc');
                            $start = microtime(true);
                            $messagesResult = $this->messages->importMessagesFromSlack($team->getToken(), $team);
                            $this->logger->info(microtime(true) - $start);

                            if ($messagesResult) {
                                dump('Update messages ' . $team->getName() . ' in ' . (number_format(microtime(true) - $start, 8)) . 'sc');
                                $this->emojis->importAllEmojiFromSlack($team);
                                $this->em->commit();
                                continue;

                            }
                        }
                    }

                }
                $this->em->rollback();
                return false;

            }
            return true;
        }
        return false;
    }


}
