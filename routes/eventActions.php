<?php
 
 use App\EventBookmarks;
 use App\EventRsvp;
  use Exception\ForbiddenException;
 use Exception\NotFoundException;

 use Ramsey\Uuid\Uuid;
 use Firebase\JWT\JWT;
 use Tuupola\Base62;

 $app->post("/bookmarkEvent/{event_id}", function ($request, $response, $arguments) {
   /* Check if token has needed scope. */
   //if ($this->token->decoded->student_id) {
    //throw new ForbiddenException("Token not allowed", 403);
    //s}
$body = [
"student_id" => $this->token->decoded->student_id,
"event_id" => $arguments["event_id"]
];
$bookmark = new EventBookmarks($body);
if (false === $check = $this->spot->mapper("App\EventBookmarks")->first([
    "event_id" => $arguments["event_id"],
    "student_id" =>  $this->token->decoded->student_id
    ])) {
    $this->spot->mapper("App\EventBookmarks")->save($bookmark);
    }else{
        throw new NotFoundException("000 liked it", 404);
    }
    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $bookmark->etag());
    $response = $this->cache->withLastModified($response, $bookmark->timestamp());
    $data["status"] = "ok";
    $data["message"] = "New bookmark created";
    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });
 
 $app->delete("/bookmarkEvent/{event_id}", function ($request, $response, $arguments) {
    /* Check if token has needed scope. */
    //if ($this->token->decoded->student_id) {
    //    throw new ForbiddenException("Token not allowed", 403);
    //}
    /* Load existing bookmark using provided event_id */
    if (false === $bookmark = $this->spot->mapper("App\EventBookmarks")->first([
        "event_id" => $arguments["event_id"],
        "student_id" =>  $this->token->decoded->student_id
        ])) {
        throw new NotFoundException("Had never bookmarked it.", 404);
    }
    $this->spot->mapper("App\EventBookmarks")->delete($bookmark);
    $data["status"] = "ok";
    $data["message"] = "Bookmark Removed";
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

 $app->post("/rsvpEvent/{event_id}", function ($request, $response, $arguments) {
     /* Check if token has needed scope. */
     //if ($this->token->decoded->student_id) {
     //   throw new ForbiddenException("Token not allowed", 403);
    //}
    $body = [
    "student_id" => $this->token->decoded->student_id,
    "event_id" => $arguments["event_id"]
    ];
    $rsvp = new EventRsvp($body);
    if (false === $check = $this->spot->mapper("App\EventRsvp")->first([
        "event_id" => $arguments["event_id"],
        "student_id" =>  $this->token->decoded->student_id
        ])) {
  $this->spot->mapper("App\EventRsvp")->save($rsvp);
  }else  {
        throw new NotFoundException("Had never rsvped it.", 404);
    };
  /* Add Last-Modified and ETag headers to response. */
  $response = $this->cache->withEtag($response, $rsvp->etag());
  $response = $this->cache->withLastModified($response, $rsvp->timestamp());
  $data["status"] = "ok";
  $data["message"] = "RSVP adeed";
  return $response->withStatus(201)
  ->withHeader("Content-Type", "application/json")
  ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  });

 $app->delete("/rsvpEvent/{event_id}", function ($request, $response, $arguments) {
     /* Check if token has needed scope. */
     //if ($this->token->decoded->student_id) {
     //   throw new ForbiddenException("Token not allowed", 403);
    //}
    /* Load existing rsvp using provided event_id */
    if (false === $rsvp = $this->spot->mapper("App\EventRsvp")->first([
        "event_id" => $arguments["event_id"],
        "student_id" =>  $this->token->decoded->student_id
        ])) {
        throw new NotFoundException("Had never rsvped it.", 404);
    };
    $this->spot->mapper("App\EventRsvp")->delete($rsvp);
    $data["status"] = "ok";
    $data["message"] = "Rsvp Removed";
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });
