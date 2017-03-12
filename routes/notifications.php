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

 $app->get("/notifications/{username}", function ($request, $response, $arguments) {

    $username =$arguments["username"];

    $follows = $this->spot->mapper("App\StudentFollow")
        ->query("SELECT * FROM followers WHERE followed_username = '". $username ."' ORDER BY timer DESC");
    $appreciate = $this->spot->mapper("App\ContentAppreciate")
        ->query("SELECT * FROM content_appreciates WHERE username = '". $username ."' ORDER BY timer DESC");
    $participants = $this->spot->mapper("App\EventRsvp")
        ->query("SELECT * FROM event_rsvps WHERE username = '". $username ."' ORDER BY timer DESC");

    $data['followers'] = $follows;
    $data['followers_count'] = count($follows);
    $data['content_appreciate'] = $appreciate;
    $data['content_appreciation_count'] = count($appreciate);
    $data['event_rsvps'] = $participants;
    $data['event_rsvp_count'] = count($participants);
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
