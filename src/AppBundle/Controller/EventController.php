<?php
namespace AppBundle\Controller;
use AppBundle\Service\EventManagerApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EventController extends Controller
{
    /**
     * @Route("/event", name="event_home")
     * @Method({"POST"})
     */
    public function eventAction(Request $request, LoggerInterface $logger, EventManagerApi $eventManagerApi)
    {

        $req = json_decode($request->getContent());
        if($req->token == $this->container->getParameter('verificationToken'))
        {
            $logger->info($request->getContent());
            $logger->info(json_decode($request->getContent())->type);
            $result = $eventManagerApi->dispatchEvent($request->getContent());

            if($result)
            {
                $logger->info("YOUPIIIIII");
                $logger->error("YOUPI");
            }
            else{
                $logger->info("snifsnif");
                $logger->error("snifsnif");
            }
        }



        return new Response('', Response::HTTP_OK);
    }
}