<?php
namespace AppBundle\Websocket\Controller;
use AppBundle\Websocket\Test;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Ratchet\ConnectionInterface;

class LoginController extends BaseController {


    public function checkAuthAction($data, ConnectionInterface $from) {
        $token = $data->user->token;
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $jwtToken = new JWTUserToken();
        $jwtToken->setRawToken($token);
        $user = $jwtManager->decode($jwtToken);
        if ($user['username']) {
            Test::$authClients->attach($from);
            Test::$userList[$from->resourceId] = $user['username'];
        }
    }
}