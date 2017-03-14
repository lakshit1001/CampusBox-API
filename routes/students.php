<?php

 

use App\Student;
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

// $app->get("/student/{username}/{type}", function ($request, $response, $arguments) {

//     /* Check if token has needed scope. */
//     //if (true === $this->token->hasScope(["student.all", "student.read"])) {
//     //    throw new ForbiddenException("Token not allowed to list students.", 403);
//     //}

//     /* Load existing student using provided id */
//     if (false === $student = $this->spot->mapper("App\Student")->first([
//         "username" => $arguments["username"]
//     ])) {
//         throw new NotFoundException("Student not found.", 404);
//     };

//     /* If-Modified-Since and If-None-Match request header handling. */
//     /* Heads up! Apache removes previously set Last-Modified header */
//      from 304 Not Modified responses. 
//     if ($this->cache->isNotModified($request, $response)) {
//         return $response->withStatus(304);
//     }

//     /* Serialize the response data. */
//     $fractal = new Manager();
//     $fractal->setSerializer(new DataArraySerializer);
//     if($arguments["username"]==""){
        
//     $resource = new Item($student, new StudentTransformer);
//     }elseif($arguments["username"]==""){

//     $resource = new Item($student, new StudentTransformer);
//     }
    
//     $data = $fractal->createData($resource)->toArray();

//     return $response->withStatus(200)
//         ->withHeader("Content-Type", "appliaction/json")
//         ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
// });



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

$app->post("/studentFollow", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $participants = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '". $body['followed_username'] ."' AND follower_username = '" .$this->token->decoded->username. "'");

    if(count($participants) > 0){
        $data["status"] = "Already Following";
    } else {
        $event['followed_username'] =  $body['followed_username'];
        $event['follower_username'] =  $this->token->decoded->username;

        $newEvent = new StudentFollow($event);
        $this->spot->mapper("App\StudentFollow")->save($newEvent);
        $data["status"] = "Successfull";

        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new Item($newEvent, new StudentFollowTransformer);
        $data = $fractal->createData($resource)->toArray();
    }

    /* Serialize the response data. */
    return $response->withStatus(201)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/studentFollow", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    /* Load existing todo using provided uid */
    $rsvp = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '". $body['followed_username'] ."' AND follower_username = '" .$this->token->decoded->username. "'");
    if(count($rsvp) <= 0){
        $data["status"] = "Not Following";
    } else {
        $rsvp = $this->spot->mapper("App\StudentFollow")->query("SELECT * FROM `followers` WHERE followed_username = '". $body['followed_username'] ."' AND follower_username = '" .$this->token->decoded->username. "'")->first();
    $this->spot->mapper("App\StudentFollow")->delete($rsvp);

    $data["status"] = "ok";
    $data["message"] = "Unfollowed";
    }

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});