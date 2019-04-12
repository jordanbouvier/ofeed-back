<?php
namespace AppBundle\Controller;
use AppBundle\Service\MessageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MessageController extends Controller
{

    /**
     * @Route("/api/message/search", name="message_search")
     * @Method({"GET"})
     */
    public function searchAction(MessageManager $mm, Request $request)
    {
        $data = $mm->searchMessages($request);
        if(!$data)
        {
            return $this->json(false, 404);
        }
        $response = new JsonResponse($data, 200, [], true);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}