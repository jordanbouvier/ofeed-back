<?php
namespace AppBundle\Controller;
use AppBundle\Entity\FileSlack;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\TeamSlack;
use AppBundle\Service\ChannelManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\ChannelSlack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer as JMS;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;




class ChannelController extends Controller
{
   /**
    * @Route("/api/team/{idTeam}/channels", name="channel_list")
    * @ParamConverter("team", options={"mapping" : {"idTeam" : "idSlack"}})
    * @Method({"GET"})
    */
   public function listAction(ChannelManager $cm, TeamSlack $team)
   {
       $data = $cm->getChannelList($team);
       if(!$data)
       {
          return $this->json($data, 404);
       }
       $response = new JsonResponse($data,  200, [], true);
       $response->headers->set('Access-Control-Allow-Origin', '*');
       $response->headers->set('Content-Type', 'application/json');
       return $response;

   }

   /**
    * @Route("/api/channel/{idChannel}", name="channel_detail")
    * @ParamConverter("channel",  options={"mapping" : {"idChannel" : "idSlack"}})
    * @Method({"GET"})
    */
   public function showAction(ChannelManager $cm, Request $request, ChannelSlack $channel)
   {
       $data = $cm->getChannelFullDetailsWithPage($channel, $request);
       if(!$data)
       {
           return $this->json($data, 404);
       }
       $response = new JsonResponse($data,  200, [], true);
       $response->headers->set('Access-Control-Allow-Origin', '*');
       $response->headers->set('Content-Type', 'application/json');
       return $response;

   }
}
