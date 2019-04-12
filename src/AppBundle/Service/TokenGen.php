<?php
namespace AppBundle\Service;


use Doctrine\ORM\EntityManagerInterface;

class TokenGen
{
    private $routeApi;

    public function __construct(RoutesApi $route)
    {
        $this->routeApi = $route;
    }
    public function getToken($id, $secret, $code)
    {
        $url = $this->routeApi->getRouteWithParams('OAUTH_ACCESS', [
           'client_id' => $id,
           'client_secret' => $secret,
           'code' => $code,
        ]);

        $infos =  file_get_contents($url);
        return $infos;
    }

}