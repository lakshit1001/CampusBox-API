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

use App\Skill;
use App\SkillTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/skills", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["skill.all", "skill.list"])) {
        throw new ForbiddenException("Token not allowed to list skills.", 403);
    }else{
       
    }

    /* Use ETag and date from Skill with most recent update. */
    $first = $this->spot->mapper("App\Skill")
        ->all()
        ->first();

    /* Add Last-Modified and ETag headers to response when atleast one skill exists. */
    // if ($first) {
    //     $response = $this->cache->withEtag($response, $first->etag());
    //     $response = $this->cache->withLastModified($response, $first->timestamp());
    // }

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $skills = $this->spot->mapper("App\Skill")
        ->all();

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($skills, new SkillTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/skills", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["skill.all", "skill.create"])) {
        throw new ForbiddenException("Token not allowed to create skills.", 403);
    }

    $body = $request->getParsedBody();

    $skill = new Skill($body);
    $this->spot->mapper("App\Skill")->save($skill);

    /* Add Last-Modified and ETag headers to response. */
    // $response = $this->cache->withEtag($response, $skill->etag());
    // $response = $this->cache->withLastModified($response, $skill->timestamp());

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($skill, new SkillTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New skill created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/skills/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["skill.all", "skill.read"])) {
        throw new ForbiddenException("Token not allowed to list skills.", 403);
    }

    /* Load existing skill using provided id */
    if (false === $skill = $this->spot->mapper("App\Skill")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Skill not found.", 404);
    };

    /* Add Last-Modified and ETag headers to response. */
    // $response = $this->cache->withEtag($response, $skill->etag());
    // $response = $this->cache->withLastModified($response, $skill->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($skill, new SkillTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "appliaction/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

// $app->patch("/skills/{id}", function ($request, $response, $arguments) {

//     /* Check if token has needed scope. */
//     if (true === $this->token->hasScope(["skill.all", "skill.update"])) {
//         throw new ForbiddenException("Token not allowed to update skills.", 403);
//     }

//     /* Load existing skill using provided id */
//     if (false === $skill = $this->spot->mapper("App\Skill")->first([
//         "id" => $arguments["id"]
//     ])) {
//         throw new NotFoundException("Skill not found.", 404);
//     };

//     /* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
//     if (false === $this->cache->hasStateValidator($request)) {
//         throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
//     }

//     /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
//     /* someone has modified the skill respond with 412 Precondition Failed. */
//     // if (false === $this->cache->hasCurrentState($request, $skill->etag(), $skill->timestamp())) {
//     //     throw new PreconditionFailedException("Skill has been modified.", 412);
//     // }

//     $body = $request->getParsedBody();
//     $skill->data($body);
//     $this->spot->mapper("App\Skill")->save($skill);

//     /* Add Last-Modified and ETag headers to response. */
//     $response = $this->cache->withEtag($response, $skill->etag());
//     $response = $this->cache->withLastModified($response, $skill->timestamp());

//     $fractal = new Manager();
//     $fractal->setSerializer(new DataArraySerializer);
//     $resource = new Item($skill, new SkillTransformer);
//     $data = $fractal->createData($resource)->toArray();
//     $data["status"] = "ok";
//     $data["message"] = "Skill updated";

//     return $response->withStatus(200)
//         ->withHeader("Content-Type", "application/json")
//         ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
// });

// $app->put("/skills/{id}", function ($request, $response, $arguments) {

//     /* Check if token has needed scope. */
//     if (true === $this->token->hasScope(["skill.all", "skill.update"])) {
//         throw new ForbiddenException("Token not allowed to update skills.", 403);
//     }

//     /* Load existing skill using provided id */
//     if (false === $skill = $this->spot->mapper("App\Skill")->first([
//         "id" => $arguments["id"]
//     ])) {
//         throw new NotFoundException("Skill not found.", 404);
//     };

//     /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
//     if (false === $this->cache->hasStateValidator($request)) {
//         throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
//     }

//     /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
//     /* someone has modified the skill respond with 412 Precondition Failed. */
//     if (false === $this->cache->hasCurrentState($request, $skill->etag(), $skill->timestamp())) {
//         throw new PreconditionFailedException("Skill has been modified.", 412);
//     }

//     $body = $request->getParsedBody();

//     /* PUT request assumes full representation. If any of the properties is */
//     /* missing set them to default values by clearing the skill object first. */
//     $skill->clear();
//     $skill->data($body);
//     $this->spot->mapper("App\Skill")->save($skill);

//     /* Add Last-Modified and ETag headers to response. */
//     $response = $this->cache->withEtag($response, $skill->etag());
//     $response = $this->cache->withLastModified($response, $skill->timestamp());

//     $fractal = new Manager();
//     $fractal->setSerializer(new DataArraySerializer);
//     $resource = new Item($skill, new SkillTransformer);
//     $data = $fractal->createData($resource)->toArray();
//     $data["status"] = "ok";
//     $data["message"] = "Skill updated";

//     return $response->withStatus(200)
//         ->withHeader("Content-Type", "application/json")
//         ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
// });

$app->delete("/skills/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["skill.all", "skill.delete"])) {
        throw new ForbiddenException("Token not allowed to delete skills.", 403);
    }

    /* Load existing skill using provided id */
    if (false === $skill = $this->spot->mapper("App\Skill")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Skill not found.", 404);
    };

    $this->spot->mapper("App\Skill")->delete($skill);

    $data["status"] = "ok";
    $data["message"] = "Skill deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
