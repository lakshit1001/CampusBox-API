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
use Facebook\Facebook;  

$app->post("/login", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $student = new Student($body);
    $student = $this->spot
                     ->mapper("App\Student")
                     ->where(['username' => $body["username"], 'password' => $body["password"]]);
    
    if(count($student) == 0){
        return $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode("error", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
    $now = new DateTime();
    $future = new DateTime("now +30 days");
    $server = $request->getServerParams();
    $jti = Base62::encode(random_bytes(16));
    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "student_id" => $student[0]->student_id
    ];
    $secret = getenv("JWT_SECRET");
    $token = JWT::encode($payload, $secret, "HS256");
    $data["status"] = "ok";
    $data["token"] = $token;

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


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
        "student_id" => $data["data"]["id"]
    ];
    $token = JWT::encode($payload, $secret, "HS256");
    $data["status"] = "ok";
    $data["token"] = $token;
    $secret = getenv("JWT_SECRET");

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post("/facebook", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();
    $fb = new \Facebook\Facebook([
  'app_id' => '1250377088376164',
  'app_secret' => '9ea27671762a7c1b1899f5b10c45f950',
  'default_graph_version' => 'v2.8',
]);
    try {
  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
  // If you provided a 'default_access_token', the '{access-token}' is optional.
  $x = $fb->get('/me?fields=email', $body['token']);
} catch(\Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
    $me = $x->getGraphUser();
    $student = new Student();
    $student = $this->spot
                     ->mapper("App\Student")
                     ->where(['email' => $me['email']]);
                     
    $now = new DateTime();
    $future = new DateTime("now +30 days");
    $server = $request->getServerParams();
    $jti = Base62::encode(random_bytes(16));
    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "student_id" => $student[0]->student_id
    ];
    $secret = getenv("JWT_SECRET");
    $token = JWT::encode($payload, $secret, "HS256");
    $data["status"] = "ok";
    $data["token"] = $token;

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

