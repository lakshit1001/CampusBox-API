<?php
/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

use App\ContentBookmarks;
use App\ContentAppreciates;

use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;

$app->post("/bookmarkContent/{content_id}", function ($request, $response, $arguments) {

  /*   Check if token has needed scope. */
 //   if (0 < $this->token->decoded->student_id) {
   //     throw new ForbiddenException("Token not allowed to create todos.", 403);
   // }

    $body = [
        "student_id" => "4",
        "content_id" => $arguments["content_id"]

    ];

    $bookmark = new ContentBookmarks($body);
    $this->spot->mapper("App\ContentBookmarks")->save($bookmark);

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
    if (0 < $this->token->decoded->student_id) {
        throw new ForbiddenException("Token not allowed to delete todos.", 403);
    }

    /* Load existing bookmark using provided content_id */
    if (false === $bookmark = $this->spot->mapper("App\ContentBookmarks")->first([
        "content_id" => $arguments["content_id"],
        "student_id" =>  $this->token->decoded->student_id

    ])) {
        throw new NotFoundException("Had never bookmarked it.", 404);
    };

    $this->spot->mapper("App\ContentBookmarks")->delete($bookmark);

    $data["status"] = "ok";
    $data["token"] = $this->token;
   $data["message"] = "Bookmark Removed";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
