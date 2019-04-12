<?php
namespace AppBundle\Websocket\Utils;
class CheckData {
    public static function checkProperties($message) {
        $errors = [];
        $data = json_decode($message);
        if (!property_exists($data, 'event')) {
            $errors[] = 'Event not found';
        }
        if(!property_exists($data, 'data')) {
            $errors[] = 'Data not found';
        }
        if(property_exists($data, 'data') && !property_exists($data->data, 'user')) {
            $errors[] = 'User not found';
        }
        if(property_exists($data, 'data') && property_exists($data->data, 'user')) {
            if(!property_exists($data->data->user, 'token')) {
                $errors[] = 'Token not found';
            }
        }
        return $errors;

    }
}