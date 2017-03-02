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
use App\StudentSkill;
// use App\SkillTransformer;
use Firebase\JWT\JWT;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;
use Tuupola\Base62;
use Facebook\Facebook;

$app->post("/signup", function ($request, $response, $arguments) {
	$body = $request->getParsedBody();
	if($body['type'] == 'facebook'){
		$fb = new \Facebook\Facebook([
			'app_id' => '1250377088376164',
			'app_secret' => '9ea27671762a7c1b1899f5b10c45f950',
			'default_graph_version' => 'v2.8',
		]);
		try {
			$x = $fb->get('/me?fields=email,name,id', $body['token']);
		} catch (\Facebook\Exceptions\FacebookResponseExpception $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch (\Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		$me = $x->getGraphUser();
		$god['name'] = $me['name'];
		$god['email'] = $me['email'];
		$student = new Student($god);
		$this->spot->mapper("App\Student")->save($student);

		// /* Serialize the response data. */
		$fractal = new Manager();
		$fractal->setSerializer(new DataArraySerializer);
		$resource = new Item($student, new StudentTransformer);
		$registered_student = $fractal->createData($resource)->toArray();
		for ($i=0; $i < count($body['skills']); $i++) { 
			$skills['student_id'] = $registered_student['data']['id'];
			$skills['skill_name'] = $body['skills'][$i]['skill_name'];
			$skills['proficiency'] = $body['skills'][$i]['proficiency'];
			$skill = new StudentSkill($skills);
			$this->spot->mapper("App\StudentSkill")->save($skill);
		}
		$now = new DateTime();
		$future = new DateTime("now +30 days");
		$server = $request->getServerParams();
		$jti = Base62::encode(random_bytes(16));
		$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"student_id" => $registered_student["data"]["id"],
		];
		$token = JWT::encode($payload, $secret, "HS256");
		$data["status"] = 'Registered Successfully';
		$data["token"] = $token;
	return $response->withStatus(201)
		->withHeader("Content-Type", "application/json")
		->withHeader("Location", $data["data"]["links"]["self"])
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
	}
});
