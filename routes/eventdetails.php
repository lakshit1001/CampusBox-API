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

use App\EventTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/liked", function ($request, $response, $arguments) {

	$test = $this->token->decoded->student_id;
	$body = $request->getParsedBody();
	$events = $this->spot->mapper("App\Event")
		->all()
	// ->where(['username' => $body["username"], 'password' => $body["password"]])
		->order(["time_created" => "DESC"]);

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Collection($events, new EventTransformer);
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
