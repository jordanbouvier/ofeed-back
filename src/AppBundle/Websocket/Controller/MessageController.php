<?php
namespace AppBundle\Websocket\Controller;
use Ratchet\ConnectionInterface;
use AppBundle\Websocket\Test;
class MessageController extends BaseController {
    public function newAction($data, ConnectionInterface $from) {
        if(!$this->checkAuth($data)) {
            return false;
        }
        if(!property_exists($data, 'message') || !property_exists($data->message, 'content')) {
            return false;
        }
        $user = $this->getUser();
        $message = [
            'author_name' => $user['username'],
            'content' => $data->message->content,
        ];
        $data = [
            'message' => $message,
            'event' => 'NEW_MESSAGE',
        ];

        $jsonData = json_encode($data);
        foreach(Test::$authClients as $client) {
            $client->send($jsonData);
        }

    }
}