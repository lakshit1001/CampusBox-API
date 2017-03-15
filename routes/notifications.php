<?php

 

 use App\StudentFollow;
 use App\StudentFollowTransformer;
 use App\ContentAppreciate;
 use App\ContentAppreciateTransformer;
 use App\EventRsvp;
 use App\EventRsvpTransformer;
 use Exception\ForbiddenException;
 use Exception\NotFoundException;
 use Exception\PreconditionFailedException;
 use Exception\PreconditionRequiredException;
 use League\Fractal\Manager;
 use League\Fractal\Resource\Collection;
 use League\Fractal\Resource\Item;
 use League\Fractal\Serializer\DataArraySerializer;

 $app->get("/notifications", function ($request, $response, $arguments) {

    $username =$this->token->decoded->username;

    $follows = $this->spot->mapper("App\StudentFollow")
        ->query("SELECT * FROM followers WHERE followed_username = '". $username ."' ORDER BY timer DESC LIMIT 5");

    $appreciate = $this->spot->mapper("App\ContentAppreciate")
        ->query("SELECT content_appreciates.content_id, content_appreciates.timer, content_appreciates.username, COUNT(event_rsvps.event_id) AS x FROM `content_appreciates`
                LEFT JOIN `contents`
                ON contents.content_id = content_appreciates.content_id
                WHERE created_by_username = '". $username ."'
                GROUP BY event_rsvps.event_id
                ORDER BY content_appreciates.timer DESC");

    $participants = $this->spot->mapper("App\EventRsvp")
        ->query("SELECT event_rsvps.event_id, event_rsvps.timer, COUNT(event_rsvps.event_id) AS x FROM `event_rsvps`
                LEFT JOIN `events`
                ON events.event_id = event_rsvps.event_id
                WHERE created_by_username = '". $username ."'
                GROUP BY event_rsvps.event_id
                ORDER BY event_rsvps.timer DESC");


        foreach ($follows as $key) {

            $newNotification1['type'] = "follower"; 
            $newNotification1['follower_username'] = $key->follower_username; 
            $newNotification1['timer'] = $key->timer; 

            $notification[]=$newNotification1; 
        }
        foreach ($appreciate as $key) {
            $newNotification2['type'] = "content_appreciate"; 
            $newNotification2['content_id'] = $key->content_id;
            $newNotification2['username'] = $key->username;
            $newNotification2['timer'] = $key->timer;                                     
            $newNotification2['total'] = $key->x; 

            $notification[]=$newNotification2; 
        }
        foreach ($participants as $key) {
            $newNotification3['type'] = "event_rsvps"; 
            $newNotification3['event_id'] = $key->event_id;
            $newNotification3['timer'] = $key->timer;                                     
            $newNotification3['total'] = $key->x; 

            $notification[]=$newNotification3; 
        }

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($notification, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
