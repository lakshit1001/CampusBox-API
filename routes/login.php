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

use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;
$app->post("/login", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $student = new Student($body);
    $student = $this->spot
                     ->mapper("App\Student")
                     ->where(['username' => $body["username"], 'password' => $body["password"]]);
    
    $requested_scopes = $request->getParsedBody();
    $valid_scopes = [
        "todo.create",
        "todo.read",
        "todo.update",
        "todo.delete",
        "todo.list",
        "todo.all"
    ];
    $scopes = array_filter($requested_scopes, function ($needle) use ($valid_scopes) {
        return in_array($needle, $valid_scopes);
    });
    $now = new DateTime();
    $future = new DateTime("now +30 days");
    $server = $request->getServerParams();
    $jti = Base62::encode(random_bytes(16));
    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "student_id" => $student[0]->student_id,
        "scope" => $scopes
    ];
    $secret = getenv("JWT_SECRET");
    $token = JWT::encode($payload, $secret, "HS256");
    $data["status"] = "ok";
    $data["token"] = $token;

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});