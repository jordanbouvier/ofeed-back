<?php
namespace AppBundle\Websocket;
use AppBundle\Websocket\Utils\WebsocketRouter;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use AppBundle\Websocket\Utils\CheckData;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Test sur les websocket

class Test implements MessageComponentInterface
{
    protected $clients;
    public static $authClients;
    public static $userList;
    protected $router;

    public function __construct(WebsocketRouter $router)
    {
        $this->clients = new \SplObjectStorage();
        self::$authClients = new \SplObjectStorage();
        self::$userList = [];
        $this->router = $router;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        // $conn->send(sprintf('New connection: Hello #%d', $conn->resourceId));
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->clients->detach($closedConnection);
        if(key_exists($closedConnection->resourceId, self::$userList)) {
            self::$authClients->detach($closedConnection);
            unset(self::$userList[$closedConnection->resourceId]);
        }

        echo sprintf('Connection #%d has disconnected\n', $closedConnection->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        $data = json_decode($message);
        $errors = CheckData::checkProperties($message);

        if(count($errors) === 0) {
            try {
                $this->router->callAction($data->event, $data->data, $from);
            } catch(\Exception $e) {
                echo $e->getMessage();
            }

        }
        else {
            $this->clients->detach($from);
        }

    }
}