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
use Firebase\JWT\JWT;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;
use Tuupola\Base62;

$app->post("/signup", function ($request, $response, $arguments) {
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

	$now = new DateTime();
	$future = new DateTime("now +30 days");
	$server = $request->getServerParams();
	$jti = Base62::encode(random_bytes(16));
	$payload = [
		"iat" => $now->getTimeStamp(),
		"exp" => $future->getTimeStamp(),
		"jti" => $jti,
		"student_id" => $data["data"]["id"],
	];
	$token = JWT::encode($payload, $secret, "HS256");
	$data["token"] = $token;
	$secret = getenv("JWT_SECRET");

	return $response->withStatus(201)
		->withHeader("Content-Type", "application/json")
		->withHeader("Location", $data["data"]["links"]["self"])
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
