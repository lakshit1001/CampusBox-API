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

use App\Student;
use App\StudentTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/students", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["student.all", "student.list"])) {
        return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($this->token->decoded->student_id, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }else{
       
    }

    /* Use ETag and date from Student with most recent update. */
    $first = $this->spot->mapper("App\Student")
        ->all()//->with('college')
        ->first();

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $students = $this->spot->mapper("App\Student")
        ->all();

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($students, new StudentTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/students", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["student.all", "student.create"])) {
        throw new ForbiddenException("Token not allowed to create students.", 403);
    }

    $body = $request->getParsedBody();

    $student = new Student($body);
    $this->spot->mapper("App\Student")->save($student);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($student, new StudentTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New student created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/students/{student_id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["student.all", "student.read"])) {
        throw new ForbiddenException("Token not allowed to list students.", 403);
    }

    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "student_id" => $arguments["student_id"]
    ])) {
        throw new NotFoundException("Student not found.", 404);
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
    $resource = new Item($student, new StudentTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "appliaction/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/students/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["student.all", "student.update"])) {
        throw new ForbiddenException("Token not allowed to update students.", 403);
    }

    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Student not found.", 404);
    };

    $body = $request->getParsedBody();
    $student->data($body);
    $this->spot->mapper("App\Student")->save($student);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($student, new StudentTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Student updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/students/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["student.all", "student.update"])) {
        throw new ForbiddenException("Token not allowed to update students.", 403);
    }

    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Student not found.", 404);
    };

    /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
    }

    $body = $request->getParsedBody();

    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the student object first. */
    $student->clear();
    $student->data($body);
    $this->spot->mapper("App\Student")->save($student);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($student, new StudentTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Student updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/students/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["student.all", "student.delete"])) {
        throw new ForbiddenException("Token not allowed to delete students.", 403);
    }

    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Student not found.", 404);
    };

    $this->spot->mapper("App\Student")->delete($student);

    $data["status"] = "ok";
    $data["message"] = "Student deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
