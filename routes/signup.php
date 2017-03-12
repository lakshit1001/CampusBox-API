<?php

use App\Student;
use App\StudentTransformer;
use App\SocialAccount;
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
	if(count($body['intrests']) > 20){
		$error['message'] = 'Tooooo many intersts ! we do appriciate it but can not save it ';
		return $response->withStatus(201)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
	}
	else if(!isset($body['roll']) || !isset($body['college_id']) ){
		$error['message'] = 'Collge or roll missing !' ;
		
		return $response->withStatus(201)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

	}
	if($body['type'] == 'facebook'){
		$fb = new \Facebook\Facebook([
			'app_id' => '1250377088376164',
			'app_secret' => '9ea27671762a7c1b1899f5b10c45f950',
			'default_graph_version' => 'v2.8',
			]);
		try {
			$x = $fb->get('/me?fields=email,name,id,gender,about,website,birthday,picture,link,cover', $body['token']);
		} catch (\Facebook\Exceptions\FacebookResponseExpception $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch (\Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		$facebookData = $x->getGraphUser();
		$student = new SocialAccount();
		$student = $this->spot
		->mapper("App\SocialAccount")
		->where(['social_id' => $facebookData['id']])
		->orWhere(['link' => $facebookData['link']]);

					//check it account is already there 

		if (count($student) > 0) {
			$data["type"] = "login";
			$now = new DateTime();
			$future = new DateTime("now +30 days");
			$server = $request->getServerParams();
			$jti = Base62::encode(random_bytes(16));

			$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $student[0]->username,
						"student_id" => $student[0]->student_id,

			];
			$secret = getenv("JWT_SECRET");
			$token = JWT::encode($payload, $secret, "HS256");
			$data["status"] = 'Already Registered, new data will not be saved.';
			$data["token"] = $token;
			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		}

				// if same account is not there but one with same emai is there just merge the accounts
		if(isset( $facebookData['email'])){
			
			$student = new SocialAccount();
			$student = $this->spot
			->mapper("App\SocialAccount")
			->where(['email' => $facebookData['email']]);
			if (count($student) > 0) {


				$social['username'] = $student[0]->username;
				$social['social_id'] = $facebookData['id'];
				$social['type'] = "facebook";
				$social['token'] = $body['token'];
				$social['name'] = isset($facebookData['name'])?$facebookData['name']:" ";
				$social['email'] = isset($facebookData['email'])? $facebookData['email']:" ";
				$social['gender'] = isset($facebookData['gender'])?$facebookData['gender']:" ";
				$social['birthday'] = isset($facebookData['birthday']) ?$facebookData->getBirthday()->format('m/d/Y'):" ";
				$social['link'] = isset($facebookData['link']) ?$facebookData['link']:" ";
				$social['about'] = isset($facebookData['about']) ?$facebookData['about']: " ";
				$social['picture'] = isset($facebookData['picture']['url'])?$facebookData['picture']['url']:" ";
				$social['cover'] = isset($facebookData['cover']['url'])?$facebookData['picture']['url']:" ";
				$socialAccount = new SocialAccount($social);
				$this->spot->mapper("App\SocialAccount")->save($socialAccount);

				$data["type"] = "login";
				$now = new DateTime();
				$future = new DateTime("now +30 days");
				$server = $request->getServerParams();
				$jti = Base62::encode(random_bytes(16));

				$payload = [
				"iat" => $now->getTimeStamp(),
				"exp" => $future->getTimeStamp(),
				"jti" => $jti,
				"username" => $student[0]->username,
				"college_id" => $student[0]->college_id,
				];
				$secret = getenv("JWT_SECRET");
				$token = JWT::encode($payload, $secret, "HS256");
				$data["status"] = 'Already Registered with this email, facebook is now connected to the existing account.';
				$data["token"] = $token;
				return $response->withStatus(201)
				->withHeader("Content-Type", "application/json")
				->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
			}
				// making a completly fresh account
		}
		else{
				//we make a new username basis of the email provided 
			if(isset($facebookData['email'])){
				$newUser['username'] = strstr($facebookData['email'], '@', TRUE);
				$student = new SocialAccount();
				$student = $this->spot
				->mapper("App\SocialAccount")
				->where(['username' =>$newUser['username']]);
							//if same username exists just use the fb id 
				if (count($student) > 0) {
					$newUser['username'] = $facebookData['id'];		
				}
					// if facebook is not sending the email id (happens with unverified users )
			}else{		
				$newUser['username'] = $facebookData['id'];		
			}

					// first add user to students table
			$newUser['name'] = isset($facebookData['name'])?$facebookData['name']:" ";
			$newUser['email'] = isset($facebookData['email'])? $facebookData['email']:" ";
			$newUser['gender'] = isset($facebookData['gender'])?$facebookData['gender']:" ";
			$newUser['birthday'] = isset($facebookData['birthday']) ?$facebookData->getBirthday()->format('m/d/Y'):" ";
			$newUser['about'] = isset($facebookData['about']) ?$facebookData['about']: "Apparently, this user prefers to keep an air of mystery about them";
			$newUser['image'] = isset($facebookData['picture']['url'])?$facebookData['picture']['url']:" ";

			$newUser['college_id'] = $body['college_id'];
			$newUser['roll_number'] = $body['roll']	;
			$newUserAccount = new Student($newUser);
			$this->spot->mapper("App\Student")->save($newUserAccount);



					// add same data to social accounts table
			$social['college_id'] = $body['college_id'];
			$social['roll_number'] = $body['roll']	;
			$social['username'] = $newUser['username'];
			$social['social_id'] = $facebookData['id'];
			$social['type'] = "facebook";
			$social['token'] = $body['token'];
			$social['name'] = isset($facebookData['name'])?$facebookData['name']:" ";
			$social['email'] = isset($facebookData['email'])? $facebookData['email']:" ";
			$social['gender'] = isset($facebookData['gender'])?$facebookData['gender']:" ";
			$social['gender'] = isset($facebookData['gender'])?$facebookData['gender']:" ";
			$social['birthday'] = isset($facebookData['birthday']) ?$facebookData->getBirthday()->format('m/d/Y'):" ";
			$social['link'] = isset($facebookData['link']) ?$facebookData['link']:" ";
			$social['about'] = isset($facebookData['about']) ?$facebookData['about']: " ";
			$social['picture'] = isset($facebookData['picture']['url'])?$facebookData['picture']['url']:" ";
			$social['cover'] = isset($facebookData['cover']['url'])?$facebookData['picture']['url']:" ";
			$socialAccount = new SocialAccount($social);
			$this->spot->mapper("App\SocialAccount")->save($socialAccount);

		}

	}
	else if ($body['type']=="google"){
		$json = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$body['access_token']);
		$googleData = json_decode($json);

		$student = new SocialAccount();
		$student = $this->spot
		->mapper("App\SocialAccount")
		->where(['social_id' => $googleData->id])
		->orWhere(['link' => $googleData->link]);

				//check it account is already there 

		if (count($student) > 0) {
			$data["type"] = "login";
			$now = new DateTime();
			$future = new DateTime("now +30 days");
			$server = $request->getServerParams();
			$jti = Base62::encode(random_bytes(16));

			$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $student[0]->username,
			"student_id" => $student[0]->student_id,
			];
			$secret = getenv("JWT_SECRET");
			$token = JWT::encode($payload, $secret, "HS256");
			$data["status"] = 'Already Registered, new data will not be saved.';
			$data["token"] = $token;
			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		}

			// if same account is not there but one with same emai is there just merge the accounts
		$student = new SocialAccount();
		$student = $this->spot
		->mapper("App\SocialAccount")
		->where(['email' => $googleData->email]);
		if (count($student) > 0) {

			$social['college_id'] = $body['college_id'];
			$social['roll_number'] = $body['roll']	;
			$social['username'] = $student[0]->username;
			$social['social_id'] = $googleData->id;
			$social['type'] = "google";
			$social['token'] = $body['access_token'];
			$social['name'] = isset($googleData->name)?$googleData->name:" ";
			$social['email'] = isset($googleData->email)? $googleData->email:" ";
			$social['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$social['birthday'] = isset($googleData->birthday) ?$googleData->birthday:" ";
			$social['link'] = isset($googleData->link) ?$googleData->link:" ";
			$social['about'] = isset($googleData->about) ?$googleData->about: " ";
			$social['picture'] = isset($googleData->picture)?$googleData->picture:" ";
			$social['cover'] = isset($googleData->cover['url'])?$googleData->picture:" ";
			$socialAccount = new SocialAccount($social);
			//$this->spot->mapper("App\SocialAccount")->save($socialAccount);

			$data["type"] = "login";
			$now = new DateTime();
			$future = new DateTime("now +30 days");
			$server = $request->getServerParams();
			$jti = Base62::encode(random_bytes(16));

			$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $student[0]->username,
						"student_id" => $student[0]->student_id,

			];
			$secret = getenv("JWT_SECRET");
			$token = JWT::encode($payload, $secret, "HS256");
			$data["status"] = 'Already Registered with this email, google is now connected to the existing account.';
			$data["token"] = $token;
			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
			// making a completly fresh account
		else{
			//we make a new username basis of the email provided 

			if(isset($googleData->email)){
				$newUser['username'] = strstr($googleData->email, '@', TRUE);
				$student = new SocialAccount();
				$student = $this->spot
				->mapper("App\SocialAccount")
				->where(['username' =>$newUser['username']]);
					//if same username exists just use the fb id 
				if (count($student) > 0) {
					$newUser['username'] = $googleData->id;		
				}
			// if google is not sending the email id (happens with unverified users )
			}else{		
				$newUser['username'] = $googleData->id;		
			}

			// first add user to students table
			$newUser['name'] = isset($googleData->name)?$googleData->name:" ";
			$newUser['email'] = isset($googleData->email)? $googleData->email:" ";
			$newUser['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$newUser['birthday'] = isset($googleData->birthday) ?$googleData->birthday:" ";
			$newUser['about'] = isset($googleData->about) ?$googleData->about: "Apparently, this user prefers to keep an air of mystery about them";
			$newUser['image'] = isset($googleData->picture)?$googleData->picture:" ";

			$newUser['college_id'] = $body['college_id'];
			$newUser['roll_number'] = $body['roll']	;
			$newUserAccount = new Student($newUser);
			$this->spot->mapper("App\Student")->save($newUserAccount);



			// add same data to social accounts table
			$social['college_id'] = $body['college_id'];
			$social['roll_number'] = $body['roll']	;
			$social['username'] = $newUser['username'];
			$social['social_id'] = $googleData->id;
			$social['type'] = "google";
			$social['token'] =$body['access_token'];
			$social['name'] = isset($googleData->name)?$googleData->name:" ";
			$social['email'] = isset($googleData->email)? $googleData->email:" ";
			$social['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$social['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$social['birthday'] = isset($googleData->birthday) ?$googleData->birthday:" ";
			$social['link'] = isset($googleData->link) ?$googleData->link:" ";
			$social['about'] = isset($googleData->about) ?$googleData->about: " ";
			$social['picture'] = isset($googleData->picture)?$googleData->picture:" ";
			$social['cover'] = isset($googleData->cover['url'])?$googleData->picture:" ";
			$socialAccount = new SocialAccount($social);
			$this->spot->mapper("App\SocialAccount")->save($socialAccount);

		}
	}
else if ($body['type']=="linkedIN"){
		$json = file_get_contents('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,picture-url,headline,location,industry,summary,specialties,positions,public-profile-url)'.$body['access_token']);
		$linkedinData = json_decode($json);
		echo $linkedinData->name;
		$student = new SocialAccount();
		$student = $this->spot
		->mapper("App\SocialAccount")
		->where(['social_id' => $linkedinData->id])
		->orWhere(['link' => $linkedinData->link]);

				//check it account is already there 

		if (count($student) > 0) {
			$data["type"] = "login";
			$now = new DateTime();
			$future = new DateTime("now +30 days");
			$server = $request->getServerParams();
			$jti = Base62::encode(random_bytes(16));

			$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $student[0]->username,
			"student_id" => $student[0]->student_id,
			];
			$secret = getenv("JWT_SECRET");
			$token = JWT::encode($payload, $secret, "HS256");
			$data["status"] = 'Already Registered, new data will not be saved.';
			$data["token"] = $token;
			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		}

			// if same account is not there but one with same emai is there just merge the accounts
		$student = new SocialAccount();
		$student = $this->spot
		->mapper("App\SocialAccount")
		->where(['email' => $linkedinData->email]);
		if (count($student) > 0) {

			$social['college_id'] = $body['college_id'];
			$social['roll_number'] = $body['roll']	;
			$social['username'] = $student[0]->username;
			$social['social_id'] = $linkedinData->id;
			$social['type'] = "google";
			$social['token'] = $body['access_token'];
			$social['name'] = isset($linkedinData->name)?$linkedinData->name:" ";
			$social['email'] = isset($linkedinData->email)? $linkedinData->email:" ";
			$social['gender'] = isset($linkedinData->gender)?$linkedinData->gender:" ";
			$social['birthday'] = isset($linkedinData->birthday) ?$linkedinData->birthday:" ";
			$social['link'] = isset($linkedinData->link) ?$linkedinData->link:" ";
			$social['about'] = isset($linkedinData->about) ?$linkedinData->about: " ";
			$social['picture'] = isset($linkedinData->picture)?$linkedinData->picture:" ";
			$social['cover'] = isset($linkedinData->cover['url'])?$linkedinData->picture:" ";
			$socialAccount = new SocialAccount($social);
			//$this->spot->mapper("App\SocialAccount")->save($socialAccount);

			$data["type"] = "login";
			$now = new DateTime();
			$future = new DateTime("now +30 days");
			$server = $request->getServerParams();
			$jti = Base62::encode(random_bytes(16));

			$payload = [
			"iat" => $now->getTimeStamp(),
			"exp" => $future->getTimeStamp(),
			"jti" => $jti,
			"username" => $student[0]->username,
						"student_id" => $student[0]->student_id,

			];
			$secret = getenv("JWT_SECRET");
			$token = JWT::encode($payload, $secret, "HS256");
			$data["status"] = 'Already Registered with this email, google is now connected to the existing account.';
			$data["token"] = $token;
			return $response->withStatus(201)
			->withHeader("Content-Type", "application/json")
			->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
			// making a completly fresh account
		else{
			//we make a new username basis of the email provided 

			if(isset($googleData->email)){
				$newUser['username'] = strstr($googleData->email, '@', TRUE);
				$student = new SocialAccount();
				$student = $this->spot
				->mapper("App\SocialAccount")
				->where(['username' =>$newUser['username']]);
					//if same username exists just use the fb id 
				if (count($student) > 0) {
					$newUser['username'] = $googleData->id;		
				}
			// if google is not sending the email id (happens with unverified users )
			}else{		
				$newUser['username'] = $googleData->id;		
			}

			// first add user to students table
			$newUser['name'] = isset($googleData->name)?$googleData->name:" ";
			$newUser['email'] = isset($googleData->email)? $googleData->email:" ";
			$newUser['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$newUser['birthday'] = isset($googleData->birthday) ?$googleData->birthday:" ";
			$newUser['about'] = isset($googleData->about) ?$googleData->about: "Apparently, this user prefers to keep an air of mystery about them";
			$newUser['image'] = isset($googleData->picture)?$googleData->picture:" ";

			$newUser['college_id'] = $body['college_id'];
			$newUser['roll_number'] = $body['roll']	;
			$newUserAccount = new Student($newUser);
			$this->spot->mapper("App\Student")->save($newUserAccount);



			// add same data to social accounts table
			$social['college_id'] = $body['college_id'];
			$social['roll_number'] = $body['roll']	;
			$social['username'] = $newUser['username'];
			$social['social_id'] = $googleData->id;
			$social['type'] = "google";
			$social['college_id'] = $newUser['college_id'];
			$social['roll_number'] = $newUser['roll_number']	;
			$social['token'] =$body['access_token'];
			$social['name'] = isset($googleData->name)?$googleData->name:" ";
			$social['email'] = isset($googleData->email)? $googleData->email:" ";
			$social['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$social['gender'] = isset($googleData->gender)?$googleData->gender:" ";
			$social['birthday'] = isset($googleData->birthday) ?$googleData->birthday:" ";
			$social['link'] = isset($googleData->link) ?$googleData->link:" ";
			$social['about'] = isset($googleData->about) ?$googleData->about: " ";
			$social['picture'] = isset($googleData->picture)?$googleData->picture:" ";
			$social['cover'] = isset($googleData->cover['url'])?$googleData->picture:" ";
			$socialAccount = new SocialAccount($social);
			$this->spot->mapper("App\SocialAccount")->save($socialAccount);

		}
	}

	//adding interests 

	for ($i=0; $i < count($body['intrests']); $i++) {
		$intrests['username'] = $social['username'];
		$intrests['interest_id'] = $body['intrests'][$i]['id'];
		$intrests['interest_name'] = $body['intrests'][$i]['title'];
		$intrest = new StudentInterest($intrests);
		$this->spot->mapper("App\StudentInterest")->save($intrest);
	}

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($newUserAccount, new StudentTransformer);
	$registered_student = $fractal->createData($resource)->toArray();

	$now = new DateTime();
	$future = new DateTime("now +30 days");
	$server = $request->getServerParams();
	$jti = Base62::encode(random_bytes(16));
	$payload = [
	"iat" => $now->getTimeStamp(),
	"exp" => $future->getTimeStamp(),
	"jti" => $jti,
	"username" => $newUser['username'],
	"college_id" => $newUser['college_id'],

	];
	$secret = getenv("JWT_SECRET");
	$token = JWT::encode($payload, $secret, "HS256");
	$data["status"] = 'Registered Successfully';
	$data["token"] = $token;
	return $response->withStatus(201)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
