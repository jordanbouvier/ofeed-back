<?php
namespace AppBundle\Service;

use AppBundle\Entity\EmojiSlack;
use AppBundle\Entity\TeamSlack;
use Doctrine\ORM\EntityManagerInterface;

class EmojiManager
{
    private $em;
    private $routeApi;

    public function __construct(EntityManagerInterface $em, RoutesApi $routes)
    {
        $this->em = $em;
        $this->routeApi = $routes;
    }

    public function addEmoji($code, $url)
    {
        $emoji = $this->em->getRepository(EmojiSlack::class)->findOneByCode($code);
        if(!$emoji)
        {
            $emoji = new EmojiSlack();
        }
        $emoji->setCode(':' . $code . ':')
            ->setUrl($url);
        $this->em->persist($emoji);
        return true;

    }

    public function importAllEmojiFromSlack(TeamSlack $team)
    {
        //NON TESTE
        $route = $this->routeApi->getRouteWithParams('EMOJI_LIST', ['token' => $team->getToken()]);
        $emojis = json_decode(file_get_contents($route));
        if($emojis->ok)
        {
            foreach($emojis->emoji as $code => $url)
            {
                $emoji = $this->em->getRepository(EmojiSlack::class)->findOneByCode(':' . $code . ':');
                if(!$emoji)
                {

                    $this->addEmoji($code, $url);
                }
            }
            $this->em->flush();
            return true;
        }
        return false;
    }

}