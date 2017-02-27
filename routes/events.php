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

use App\Event;
use App\EventTransformer;
use Exception\ForbiddenException;
use Exception\NotFoundException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/events", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	// if (true === $this->token->hasScope(["event.all", "event.list"])) {
	//     throw new ForbiddenException("Token not allowed to list events.", 403);
	// }else{

	// }

	$test = $this->token->decoded->student_id;

	/* Use ETag and date from Event with most recent update. */
	$first = $this->spot->mapper("App\Event")
		->all()
		->order(["time_created" => "DESC"])
		->first();

	/* Add Last-Modified and ETag headers to response when atleast on event exists. */
	if ($first) {
		$response = $this->cache->withEtag($response, $first->etag());
		$response = $this->cache->withLastModified($response, $first->timestamp());
	}

	/* If-Modified-Since and If-None-Match request header handling. */
	/* Heads up! Apache removes previously set Last-Modified header */
	/* from 304 Not Modified responses. */
	if ($this->cache->isNotModified($request, $response)) {
		return $response->withStatus(304);
	}

	$events = $this->spot->mapper("App\Event")
		->all()
		->order(["time_created" => "DESC"]);

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Collection($events, new EventTransformer(['student_id' => $test]));
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/events", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.create"])) {
		throw new ForbiddenException("Token not allowed to create events.", 403);
	}

	$body = $request->getParsedBody();

	$event = new Event($body);
	$this->spot->mapper("App\Event")->save($event);

	/* Add Last-Modified and ETag headers to response. */
	$response = $this->cache->withEtag($response, $event->etag());
	$response = $this->cache->withLastModified($response, $event->timestamp());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($event, new EventTransformer);
	$data = $fractal->createData($resource)->toArray();
	$data["status"] = "ok";
	$data["message"] = "New event created";

	return $response->withStatus(201)
		->withHeader("Content-Type", "application/json")
		->withHeader("Location", $data["data"]["links"]["self"])
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.read"])) {
		throw new ForbiddenException("Token not allowed to list events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"events_id" => $arguments["id"],
	])) {
		throw new NotFoundException("Event not found.", 404);
	};

	/* Add Last-Modified and ETag headers to response. */
	$response = $this->cache->withEtag($response, $event->etag());
	$response = $this->cache->withLastModified($response, $event->timestamp());

	/* If-Modified-Since and If-None-Match request header handling. */
	/* Heads up! Apache removes previously set Last-Modified header */
	/* from 304 Not Modified responses. */
	if ($this->cache->isNotModified($request, $response)) {
		return $response->withStatus(304);
	}

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($event, new EventTransformer);
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.update"])) {
		throw new ForbiddenException("Token not allowed to update events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
	])) {
		throw new NotFoundException("Event not found.", 404);
	};

	/* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
	if (false === $this->cache->hasStateValidator($request)) {
		throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
	}

	/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
	/* someone has modified the event respond with 412 Precondition Failed. */
	if (false === $this->cache->hasCurrentState($request, $event->etag(), $event->timestamp())) {
		throw new PreconditionFailedException("Event has been modified.", 412);
	}

	$body = $request->getParsedBody();
	$event->data($body);
	$this->spot->mapper("App\Event")->save($event);

	/* Add Last-Modified and ETag headers to response. */
	$response = $this->cache->withEtag($response, $event->etag());
	$response = $this->cache->withLastModified($response, $event->timestamp());

	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($event, new EventTransformer);
	$data = $fractal->createData($resource)->toArray();
	$data["status"] = "ok";
	$data["message"] = "Event updated";

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.update"])) {
		throw new ForbiddenException("Token not allowed to update events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
	])) {
		throw new NotFoundException("Event not found.", 404);
	};

	/* PUT requires If-Unmodified-Since or If-Match request header to be present. */
	if (false === $this->cache->hasStateValidator($request)) {
		throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
	}

	/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
	/* someone has modified the event respond with 412 Precondition Failed. */
	if (false === $this->cache->hasCurrentState($request, $event->etag(), $event->timestamp())) {
		throw new PreconditionFailedException("Event has been modified.", 412);
	}

	$body = $request->getParsedBody();

	/* PUT request assumes full representation. If any of the properties is */
	/* missing set them to default values by clearing the event object first. */
	$event->clear();
	$event->data($body);
	$this->spot->mapper("App\Event")->save($event);

	/* Add Last-Modified and ETag headers to response. */
	$response = $this->cache->withEtag($response, $event->etag());
	$response = $this->cache->withLastModified($response, $event->timestamp());

	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($event, new EventTransformer);
	$data = $fractal->createData($resource)->toArray();
	$data["status"] = "ok";
	$data["message"] = "Event updated";

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.delete"])) {
		throw new ForbiddenException("Token not allowed to delete events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
	])) {
		throw new NotFoundException("Event not found.", 404);
	};

	$this->spot->mapper("App\Event")->delete($event);

	$data["status"] = "ok";
	$data["message"] = "Event deleted";

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
