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

use App\College;
use App\CollegeTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/colleges", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.list"])) {
        throw new ForbiddenException("Token not allowed to list colleges.", 403);
    }else{
       
    }

    /* Use ETag and date from College with most recent update. */
    $first = $this->spot->mapper("App\College")
        ->all()
        ->first();

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $colleges = $this->spot->mapper("App\College")
        ->all()->with('students');

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($colleges, new CollegeTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/colleges", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.create"])) {
        throw new ForbiddenException("Token not allowed to create colleges.", 403);
    }

    $body = $request->getParsedBody();

    $college = new College($body);
    $this->spot->mapper("App\College")->save($college);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($college, new CollegeTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New college created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/colleges/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.read"])) {
        throw new ForbiddenException("Token not allowed to list colleges.", 403);
    }

    /* Load existing college using provided id */
    if (false === $college = $this->spot->mapper("App\College")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("College not found.", 404);
    };

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($college, new CollegeTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "appliaction/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/colleges/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.update"])) {
        throw new ForbiddenException("Token not allowed to update colleges.", 403);
    }

    /* Load existing college using provided id */
    if (false === $college = $this->spot->mapper("App\College")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("College not found.", 404);
    };

    $body = $request->getParsedBody();
    $college->data($body);
    $this->spot->mapper("App\College")->save($college);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($college, new CollegeTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "College updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/colleges/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.update"])) {
        throw new ForbiddenException("Token not allowed to update colleges.", 403);
    }

    /* Load existing college using provided id */
    if (false === $college = $this->spot->mapper("App\College")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("College not found.", 404);
    };

    /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
    }

    $body = $request->getParsedBody();

    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the college object first. */
    $college->clear();
    $college->data($body);
    $this->spot->mapper("App\College")->save($college);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($college, new CollegeTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "College updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/colleges/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["college.all", "college.delete"])) {
        throw new ForbiddenException("Token not allowed to delete colleges.", 403);
    }

    /* Load existing college using provided id */
    if (false === $college = $this->spot->mapper("App\College")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("College not found.", 404);
    };

    $this->spot->mapper("App\College")->delete($college);

    $data["status"] = "ok";
    $data["message"] = "College deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
