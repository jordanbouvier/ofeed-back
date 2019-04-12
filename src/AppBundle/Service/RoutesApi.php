<?php
namespace AppBundle\Service;

class RoutesApi
{
    const CHANNELS_HISTORY = 'https://slack.com/api/channels.history';
    const TEAM_INFO = 'https://slack.com/api/team.info';
    const USERS_LIST = 'https://slack.com/api/users.list';
    const FILES_LIST = 'https://slack.com/api/files.list';
    const OAUTH_ACCESS = 'https://slack.com/api/oauth.access';
    const CHANNELS_LIST = 'https://slack.com/api/channels.list';
    const EMOJI_LIST =  'https://slack.com/api/emoji.list';

    public function getRouteWithParams($routeName, $options = null)
    {
      $route = constant('static::' . $routeName);

      if($options)
      {
          $keys = array_keys($options);
          foreach ($options as $key => $option)
          {
              $init = false;
              if($key == $keys[0])
              {
                $init = true;
              }
              $route = $this->addRouteParam($route,  $key, $option, $init);
          }

      }
      return $route;
    }

    public function addRouteParam($route, $option, $value, $init = false)
    {
        $route .= $init ? '?' : '&';
        $route .= $option . '=' . $value;
        return $route;
    }
}