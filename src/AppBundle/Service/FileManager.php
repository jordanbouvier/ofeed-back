<?php
namespace AppBundle\Service;
use AppBundle\Entity\ChannelSlack;
use AppBundle\Entity\FileSlack;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\UserSlack;
use AppBundle\Entity\TeamSlack;

class FileManager
{
    private $em;
    private $reactionService;
    private $routeApi;

    public function __construct(EntityManagerInterface $em, ReactionList $reactionService, RoutesApi $routes)
    {
        $this->em = $em;
        $this->reactionService = $reactionService;
        $this->routeApi = $routes;
    }

    public function addFile($file, $channel, $init = false)
    {

        $newFile = $this->em->getRepository(FileSlack::class)->findOneByIdSlack($file->id);
        if(!$newFile)
        {
            $newFile = new FileSlack();
        }

        $newFile->setIdSlack($file->id)
            ->setCreatedAt($file->created)
            ->setName($file->name)
            ->setTitle($file->title)
            ->setFileType($file->filetype)
            ->setMimeType($file->mimetype)
            ->setPermalink($file->url_private)
            ->setPrettyType($file->pretty_type)
            ->setSize($file->size);
        if(property_exists($file, 'pinned_to'))
        {
            $newFile->setIsPinned(true);
        }
        $user = $this->em->getRepository(UserSlack::class)->findOneByIdSlack($file->user);
        $newFile->setUser($user);

        $newFile->setChannel($channel);
        if (property_exists($file, 'reactions')) {
            $newFile = $this->reactionService->addReactions($newFile, $file->reactions, $init);
        }

        $this->em->persist($newFile);
        if(!$init)
        {
            return $newFile;
        }
    }
    public function importFilesFromSlack(TeamSlack $team)
    {
        $channels = $this->em->getRepository(ChannelSlack::class)->findByTeam($team);

        foreach ($channels as $channel) {
            $continue = true;
            $page = 1;
            while($continue) {
                $url = $this->routeApi->getRouteWithParams('FILES_LIST', [
                    'token' => $team->getToken(),
                    'channel' => $channel->getIdSlack(),
                    'page' => $page,
                ]);

                $files = json_decode(file_get_contents($url));
                if (!$files->ok) {
                    return false;
                }

                foreach ($files->files as $file) {
                    $this->addFile($file, $channel, true);
                }
                if($page >= $files->paging->pages)
                {
                    $continue = false;
                }
                else
                {
                    $page = $files->paging->page + 1;
                }
            }

        }
        $this->em->flush();
        return true;
    }
}