<?php
namespace AppBundle\Controller;
use AppBundle\Entity\MessageSlack;
use AppBundle\Entity\TeamSlack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\ChannelSlack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer as JMS;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TeamController extends Controller
{
    /**
     * @Route("/api/team/", name="team_list")
     * @Method({"GET"})
     */
    public function listAction(EntityManagerInterface $em)
    {
        $teams = $em->getRepository(TeamSlack::class)->findAll();
        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($teams, 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list']));

        $response =  new JsonResponse($data, 200, [], true);
        return $response;
    }

    /**
     * @Route("/team/{idSlack}/users", name="team_user_list")
     */
    public function userList(TeamSlack $team)
    {
        $serializer = JMS\SerializerBuilder::create()->build();
        $data = $serializer->serialize($team->getUsers(), 'json', JMS\SerializationContext::create()->setGroups(['Default','team_list']));
        return new JsonResponse($data, 200, [], true);
    }

}