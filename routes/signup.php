<?php
 
use App\Student;
use App\StudentTransformer;
use App\StudentSkill;
use App\StudentInterest;
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
			$x = $fb->get('/me?fields=email,name,id,gender,about,website,birthday,picture', $body['token']);
		} catch (\Facebook\Exceptions\FacebookResponseExpception $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch (\Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		$facebookData = $x->getGraphUser();
		$student = new Student();
				$student = $this->spot
				->mapper("App\Student")
				->where(['email' => $facebookData['email']]);

				if (count($student) > 0) {
					$data["registered"] = true;
					$now = new DateTime();
					$future = new DateTime("now +30 days");
					$server = $request->getServerParams();
					$jti = Base62::encode(random_bytes(16));
					
					$payload = [
						"iat" => $now->getTimeStamp(),
						"exp" => $future->getTimeStamp(),
						"jti" => $jti,
						"username" => $student[0]->username,
					];
					$secret = getenv("JWT_SECRET");
					$token = JWT::encode($payload, $secret, "HS256");
					$data["status"] = 'Already Registered, new data will not be saved.';
					$data["token"] = $token;
					return $response->withStatus(201)
						->withHeader("Content-Type", "application/json")
						->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

				}
		$god['name'] = $facebookData['name']?$facebookData['name']:"";
		$god['gender'] = $facebookData['gender']?$facebookData['gender']:"";
		$god['birthday'] = isset($facebookData['birthday']) ?$facebookData['birthday']: null;
		$god['about'] = isset($facebookData['about']) ?$facebookData['about']: "Apparently, this user prefers to keep an air of mystery about them";
		$god['college_id'] = $body['college_id'];
		$god['image'] = $facebookData['picture']['url']?$facebookData['picture']['url']:"";
		$god['email'] = $facebookData['email']?:"noemail@campusbox.org";
		$god['roll_number'] = $body['roll']	;
		$student = new Student($god);
		$this->spot->mapper("App\Student")->save($student);

		// /* Serialize the response data. */
		$fractal = new Manager();
		$fractal->setSerializer(new DataArraySerializer);
		$resource = new Item($student, new StudentTransformer);
		$registered_student = $fractal->createData($resource)->toArray();

		if(count($body['skills']) > 5){
			$error['message'] = 'Skills Limit exceed 5';
			return $response->withStatus(201)
				->withHeader("Content-Type", "application/json")
				->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		for ($i=0; $i < count($body['skills']); $i++) { 
			$skills['student_id'] = $registered_student['data']['id'];
			$skills['skill_name'] = $body['skills'][$i]['name'];
			$skill = new StudentSkill($skills);
			$this->spot->mapper("App\StudentSkill")->save($skill);
		}
		if(count($body['intrests']) > 20){
			$error['message'] = '20 intrests, Seriously?';
			return $response->withStatus(201)
				->withHeader("Content-Type", "application/json")
				->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		for ($i=0; $i < count($body['intrests']); $i++) {
			$intrests['username'] = $registered_student['data']['id'];
			$intrests['interest_id'] = $body['intrests'][$i]['id'];
			$intrests['title'] = $body['intrests'][$i]['title'];
			$intrest = new StudentInterest($intrests);
			$this->spot->mapper("App\StudentInterest")->save($intrest);
		}
		$now = new DateTime();
		$future = new DateTime("now +30 days");
		$server = $request->getServerParams();
		$jti = Base62::encode(random_bytes(16));
		$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $registered_student["data"]["id"],
		];
		$secret = getenv("JWT_SECRET");
		$token = JWT::encode($payload, $secret, "HS256");
		$data["status"] = 'Registered Successfully';
		$data["token"] = $token;
		return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
	}
	else if ($body['type']=="google"){
		$json = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$body['token']);
		$googleData = json_decode($json);
		echo $googleData->name;
		$student1 = $this->spot
		->mapper("App\Student")
		->where(['email' => $googleData->email]);
		if (count($student1) != 0) {
			$data["registered"] = true;
			$data["name"] = $googleData->name;
			$data["email"] = $googleData->email;

			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		else{


		$god['name'] = $googleData->name;
		$god['gender'] = $googleData->gender;
		$god['birthday'] = isset($googleData['birthday']) ?$googleData['birthday']: null;
		$god['about'] = "Apparently, this user prefers to keep an air of mystery about them";
		$god['college_id'] = $body['college_id'];
		$god['image'] = $googleData->picture;
		$god['email'] = $googleData->email;
		$god['roll_number'] = $body['roll']	;
		$student = new Student($god);
		//$this->spot->mapper("App\Student")->save($student);

		// /* Serialize the response data. */
		$fractal = new Manager();
		$fractal->setSerializer(new DataArraySerializer);
		$resource = new Item($student, new StudentTransformer);
		$registered_student = $fractal->createData($resource)->toArray();
		if(count($body['skills']) > 5){
			$error['message'] = 'Skills Limit exceed 5';
			return $response->withStatus(201)
				->withHeader("Content-Type", "application/json")
				->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		for ($i=0; $i < count($body['skills']); $i++) { 
			$skills['username'] = $registered_student['data']['id'];
			$skills['skill_name'] = $body['skills'][$i]['name'];
			$skill = new StudentSkill($skills);
			$this->spot->mapper("App\StudentSkill")->save($skill);
		}
		if(count($body['intrests']) > 20){
			$error['message'] = '20 intrests, Seriously?';
			return $response->withStatus(201)
				->withHeader("Content-Type", "application/json")
				->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		for ($i=0; $i < count($body['intrests']); $i++) {
			$intrests['username'] = $registered_student['data']['id'];
			$intrests['interest_id'] = $body['intrests'][$i];
			$intrest = new StudentInterest($intrests);
			$this->spot->mapper("App\StudentInterest")->save($intrest);
		}
		$now = new DateTime();
		$future = new DateTime("now +30 days");
		$server = $request->getServerParams();
		$jti = Base62::encode(random_bytes(16));
		$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $registered_student["data"]["id"],
		];
		$secret = getenv("JWT_SECRET");
		$token = JWT::encode($payload, $secret, "HS256");
		$data["status"] = 'Registered Successfully';
		$data["token"] = $token;
		return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

	}
}
});
