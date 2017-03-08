<?php
 

 use App\ContentBookmarks;
 use App\ContentAppreciate;
  use Exception\ForbiddenException;
 use Exception\NotFoundException;

 use Ramsey\Uuid\Uuid;
 use Firebase\JWT\JWT;
 use Tuupola\Base62;

 $app->post("/bookmarkContent/{content_id}", function ($request, $response, $arguments) {
   /* Check if token has needed scope. */
   //if ($this->token->decoded->username) {
    //throw new ForbiddenException("Token not allowed", 403);
    //s}
$body = [
"username" => $this->token->decoded->username,
"content_id" => $arguments["content_id"]
];
$bookmark = new ContentBookmarks($body);
if (false === $check = $this->spot->mapper("App\ContentBookmarks")->first([
    "content_id" => $arguments["content_id"],
    "username" =>  $this->token->decoded->username
    ])) {
    $this->spot->mapper("App\ContentBookmarks")->save($bookmark);
    }else{
        throw new NotFoundException("Already liked", 404);
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
 
 $app->delete("/bookmarkContent/{content_id}", function ($request, $response, $arguments) {
    /* Check if token has needed scope. */
    //if ($this->token->decoded->username) {
    //    throw new ForbiddenException("Token not allowed", 403);
    //}
    /* Load existing bookmark using provided content_id */
    if (false === $bookmark = $this->spot->mapper("App\ContentBookmarks")->first([
        "content_id" => $arguments["content_id"],
        "username" =>  $this->token->decoded->username
        ])) {
        throw new NotFoundException("Had never bookmarked it.", 404);
    }
    $this->spot->mapper("App\ContentBookmarks")->delete($bookmark);
    $data["status"] = "ok";
    $data["message"] = "Bookmark Removed";
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

 $app->post("/appreciateContent/{content_id}", function ($request, $response, $arguments) {
     /* Check if token has needed scope. */
     //if ($this->token->decoded->username) {
     //   throw new ForbiddenException("Token not allowed", 403);
    //}
    $body = [
    "username" => $this->token->decoded->username,
    "content_id" => $arguments["content_id"]
    ];
    $appreciate = new ContentAppreciate($body);
    if (false === $check = $this->spot->mapper("App\ContentAppreciate")->first([
        "content_id" => $arguments["content_id"],
        "username" =>  $this->token->decoded->username
        ])) {
  $this->spot->mapper("App\ContentAppreciate")->save($appreciate);
  }else  {
        throw new NotFoundException("Had never appreciateed it.", 404);
    };
  /* Add Last-Modified and ETag headers to response. */
  $response = $this->cache->withEtag($response, $appreciate->etag());
  $response = $this->cache->withLastModified($response, $appreciate->timestamp());
  $data["status"] = "ok";
  $data["message"] = "RSVP adeed";
  return $response->withStatus(201)
  ->withHeader("Content-Type", "application/json")
  ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  });

 $app->delete("/appreciateContent/{content_id}", function ($request, $response, $arguments) {
     /* Check if token has needed scope. */
     //if ($this->token->decoded->username) {
     //   throw new ForbiddenException("Token not allowed", 403);
    //}
    /* Load existing appreciate using provided content_id */
    if (false === $appreciate = $this->spot->mapper("App\ContentAppreciate")->first([
        "content_id" => $arguments["content_id"],
        "username" =>  $this->token->decoded->username
        ])) {
        throw new NotFoundException("Had never appreciateed it.", 404);
    };
    $this->spot->mapper("App\ContentAppreciate")->delete($appreciate);
    $data["status"] = "ok";
    $data["message"] = "Rsvp Removed";
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });
