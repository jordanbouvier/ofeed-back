<?php

namespace AppBundle\Service;

use AppBundle\Entity\TeamSlack;


class TeamInfo
{
    private $routeApi;

    public function __construct(RoutesApi $routes)
    {
        $this->routeApi = $routes;
    }
    public function getTeamInfo($token)
    {
        $url = $this->routeApi->getRouteWithParams('TEAM_INFO', ['token' =>$token]);
        $teamInfo = json_decode(file_get_contents($url));

        if($teamInfo->ok)
        {
            $team = new TeamSlack();
            $team->setIdSlack($teamInfo->team->id)
                ->setName($teamInfo->team->name)
                ->setDomain($teamInfo->team->domain)
                ->setEmailDomain($teamInfo->team->email_domain)
                ->setToken($token);
            if(property_exists($teamInfo->team, 'icon'))
            {
                $team->setIcon($teamInfo->team->icon->image_34);
            }

            return $team;

        }

        return false;
    }
}