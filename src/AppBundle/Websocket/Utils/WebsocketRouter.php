<?php
namespace AppBundle\Websocket\Utils;
use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Websocket\Utils\Routes;

class WebsocketRouter {

    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function callAction($action, $data, ConnectionInterface $from) {
        $actionName = strtoupper($action);
        $controllerInfo = constant("AppBundle\Websocket\Utils\Routes::$actionName");
        if ($controllerInfo) {
            $controllerData = explode(':',$controllerInfo);
            $controllerClass = 'AppBundle\Websocket\Controller\\' . $controllerData[0] . 'Controller';
            if (class_exists($controllerClass)) {
                $controllerName = 'AppBundle\Websocket\Controller\\' . $controllerData[0] . 'Controller';
                $controller = new $controllerName($this->container);
                $action = $controllerData[1] . 'Action';
                if (method_exists($controller, $action)) {
                    $result = $controller->{$action}($data, $from);
                    return $result;
                }
            }

            return false;
        }
    }
}