<?php
namespace AppBundle\Command;

use AppBundle\Websocket\Utils\WebsocketRouter;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use AppBundle\Websocket\Test;

class TestServerStart extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('websocket:app:start')
        ->setDescription('Start websocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = new WebsocketRouter($this->getContainer());
        $output->write('Starting websocket server');
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Test($router)
                )
            ),8080, '127.0.0.1'

        );
        $server->run();
    }
}