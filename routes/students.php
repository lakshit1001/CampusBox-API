<?php
use App\Student;
use App\StudentSkill;
use App\studentFollow;
use App\EventTransformer;
use App\StudentTransformer;
use App\StudentFollowTransformer;
use App\ContentTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/student/{username}", function ($request, $response, $arguments) {
    $test = $this->token->decoded->username;
    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "username" => $arguments["username"]
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
$resource = new Item($student, new StudentTransformer(['username' => $test, 'type' => 'get']));
$data = $fractal->createData($resource)->toArray();

return $response->withStatus(200)
->withHeader("Content-Type", "appliaction/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->get("/myProfile", function ($request, $response, $arguments) {

    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "username" => $this->token->decoded->username
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

$app->get("/studentEvents/{username}", function ($request, $response, $arguments) {

    /* Use ETag and date from Event with most recent update. */
    $first = $this->spot->mapper("App\Event")
    ->all()
    ->where(["created_by_username" => $arguments["username"]])
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
    $test ="4";
    $events = $this->spot->mapper("App\Event")
    ->all()
    ->order(["time_created" => "DESC"]);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    if (isset($_GET['include'])) {
        $fractal->parseIncludes($_GET['include']);
    }
    $resource = new Collection($events, new EventTransformer(['username' => $test]));
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/studentContents/{username}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    // if (true === $this->token->hasScope(["content.all", "content.list"])) {
    //     throw new ForbiddenException("Token not allowed to list contents.", 403);
    // }else{

    // }

    //$test = $this->token->decoded->username;

    /* Use ETag and date from Content with most recent update. */
    $first = $this->spot->mapper("App\Content")
    ->all()
    ->order(["timer" => "DESC"])
    ->first();

    /* Add Last-Modified and ETag headers to response when atleast on content exists. */
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

    $contents = $this->spot->mapper("App\Content")
    ->all()
    ->where(["created_by_username" => $arguments["username"]])
    ->order(["timer" => "DESC"]);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    if (isset($_GET['include'])) {
        $fractal->parseIncludes($_GET['include']);
    }
    $resource = new Collection($contents, new ContentTransformer(['username' => $test]));
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/students/{username}", function ($request, $response, $arguments) {

    /* Load existing student using provided username */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "username" => $arguments["username"]
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

$app->post("/about", function ($request, $response, $arguments) {

  
    /* Load existing student using provided id */
    if (false === $student = $this->spot->mapper("App\Student")->first([
        "username" => $this->token->decoded->username
        ])) {
        throw new NotFoundException("Student not found.", 404);
};


$body = $request->getParsedBody();

/* PUT request assumes full representation. If any of the properties is */
/* missing set them to default values by clearing the student object first. */
$entity = $this->spot->mapper("App\Student")->first(['username' => $this->token->decoded->username]);
if ($entity) {
    $entity->about = $body;
    $this->spot->mapper("App\Student")->update($entity);
}

$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($entity, new StudentTransformer);
$data = $fractal->createData($resource)->toArray();
$data["status"] = "ok";
$data["message"] = "Student updated";
$data["body"] = $body;

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/addStudentSkills", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $skills['skills'] = $body['skills'];

    foreach ($skills['skills'] as $key ) {
        
        $newSkills['skill_name'] = $key['name'];
        $newSkills['username'] = $this->token->decoded->username;

        $newSkill = new StudentSkill($newSkills);
        $this->spot->mapper("App\StudentSkill")->save($newSkill);
    }
    /* Serialize the response data. */
    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode("Skills added", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/studentFollow/{username}", function ($request, $response, $arguments) {
    if($arguments['username']!=$this->token->decoded->username){

        $participants = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '".  $arguments['username'] ."' AND follower_username = '" .$this->token->decoded->username. "'");

        if(count($participants) > 0){
            $data["status"] = "Already Following";
        } else {
            $event['followed_username'] =  $arguments['username'];
            $event['follower_username'] =  $this->token->decoded->username;

            $newEvent = new StudentFollow($event);
            $this->spot->mapper("App\StudentFollow")->save($newEvent);

            $fractal = new Manager();
            $fractal->setSerializer(new DataArraySerializer);
            $resource = new Item($newEvent, new StudentFollowTransformer);
            $data = $fractal->createData($resource)->toArray();
        }

        /* Serialize the response data. */
        return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode("don't be that narsistic", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/studentFollow/{username}", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    /* Load existing todo using provided uid */
    $rsvp = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '". $arguments['username'] ."' AND follower_username = '" .$this->token->decoded->username. "'");
    if(count($rsvp) <= 0){
        $data["status"] = "Not Following";
    } else {
        $rsvp = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '". $arguments['username'] ."' AND follower_username = '" .$this->token->decoded->username. "'")->first();
        $this->spot->mapper("App\StudentFollow")->delete($rsvp);

        $data["status"] = "ok";
        $data["message"] = "Unfollowed";
    }

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/userImage", function ($request, $response, $arguments) {

    $username =$this->token->decoded->username;

    $follows = $this->spot->mapper("App\Student")
        ->query("
                SELECT name, image
                FROM students
                WHERE username = '". $username ."' ");
        $data['username'] = $this->token->decoded->username;
        $data['name'] = $follows[0]->name;
        $data['image'] = $follows[0]->image;

        return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });
