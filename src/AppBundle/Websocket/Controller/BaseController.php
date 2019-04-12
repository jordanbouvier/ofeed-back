<?php
namespace AppBundle\Websocket\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use AppBundle\Websocket\Test;


// Test sur les websocket
abstract class BaseController {
    protected $container;
    protected $user;

    /**
     * BaseController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    protected function getUser() {
        return $this->user;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkAuth($data) {
        $token = $data->user->token;
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $jwtToken = new JWTUserToken();
        $jwtToken->setRawToken($token);
        $user = $jwtManager->decode($jwtToken);
        if ($user['username']) {
            $this->user = $user;
            $userExists = array_search($user['username'], Test::$userList);
            if($userExists !== false) {
                return true;
            }
        }
        return false;
    }
}