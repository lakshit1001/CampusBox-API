<?php



use App\Content;
use App\ContentTransformer;
use Exception\ForbiddenException;
use Exception\NotFoundException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/contentSorted", function ($request, $response, $arguments) {
$user_college_id = 2;
$username= 'lakshit1001';
    $content = $this->spot->mapper("App\Content")
    ->query("SELECT
         contents.content_id,
         contents.created_by_username,
         contents.timer,
         contents.college_id,
         contents.title,
         contents.content_type_id,
         student_interests.username ,
         count(content_appreciates.content_id) as likes,

        CASE WHEN (student_interests.username = '".$username."') 
                    THEN 6 ELSE 0 END AS interestScore,
        
        CASE WHEN (contents.college_id = ".$user_college_id.") 
                    THEN 3 ELSE 0 END AS interScore,
        
        CASE WHEN (followers.follower_username = '".$username."') 
                    THEN 0 ELSE 8 END AS followScore,
        
        CASE WHEN content_appreciates.content_id IS NULL 
                    THEN 0 ELSE LOG(COUNT(content_appreciates.content_id))  END AS appriciateScore

        FROM contents
        
        LEFT JOIN student_interests
        ON  contents.content_type_id =student_interests.interest_id 
        
        LEFT JOIN followers
        ON contents.created_by_username = followers.followed_username
        
        LEFT JOIN content_appreciates
        ON contents.content_id = content_appreciates.content_id
        
        GROUP BY contents.content_id
        ORDER BY interestScore+interScore+followScore DESC,contents.timer ;");

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

 });
$app->get("/contents[/{content_type_id}]", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	// if (true === $this->token->hasScope(["content.all", "content.list"])) {
	//     throw new ForbiddenException("Token not allowed to list contents.", 403);
	// }else{

	// }
	$test = $this->token->decoded->username;

	/* Use ETag and date from Content with most recent update. */
	if(isset($arguments['content_type_id'])){
		$first = $this->spot->mapper("App\Content")
			->all()
			->where(["content_type_id"=>$arguments['content_type_id']])
			->order(["timer" => "DESC"])
			->first();
	}else{

		$first = $this->spot->mapper("App\Content")
		->all()
		->order(["timer" => "DESC"])
		->first();
	}

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
if(isset($arguments['content_type_id'])){
		$contents = $this->spot->mapper("App\Content")
			->all()
			->where(["content_type_id"=>$arguments['content_type_id']])
			->order(["timer" => "DESC"]);
	}else{

		$contents = $this->spot->mapper("App\Content")
		->all()
		->order(["timer" => "DESC"]);
	}

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

$app->post("/contents", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["content.all", "content.create"])) {
		throw new ForbiddenException("Token not allowed to create contents.", 403);
	}

	$body = $request->getParsedBody();

	$content = new Content($body);
	$this->spot->mapper("App\Content")->save($content);

	/* Add Last-Modified and ETag headers to response. */
	$response = $this->cache->withEtag($response, $content->etag());
	$response = $this->cache->withLastModified($response, $content->timestamp());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($content, new ContentTransformer);
	$data = $fractal->createData($resource)->toArray();
	$data["status"] = "ok";
	$data["message"] = "New content created";

	return $response->withStatus(201)
	->withHeader("Content-Type", "application/json")
	->withHeader("Location", $data["data"]["links"]["self"])
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/content/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	//if (true === $this->token->hasScope(["content.all", "content.read"])) {
	//	throw new ForbiddenException("Token not allowed to list contents.", 403);
	//}

	/* Load existing content using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"content_id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

/* Add Last-Modified and ETag headers to response. */
$response = $this->cache->withEtag($response, $content->etag());
$response = $this->cache->withLastModified($response, $content->timestamp());

/* If-Modified-Since and If-None-Match request header handling. */
/* Heads up! Apache removes previously set Last-Modified header */
/* from 304 Not Modified responses. */
if ($this->cache->isNotModified($request, $response)) {
	return $response->withStatus(304);
}

/* Serialize the response data. */
$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($content, new ContentTransformer);
$data = $fractal->createData($resource)->toArray();

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/contents/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["content.all", "content.update"])) {
		throw new ForbiddenException("Token not allowed to update contents.", 403);
	}

	/* Load existing content using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

/* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
if (false === $this->cache->hasStateValidator($request)) {
	throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
}

/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
/* someone has modified the content respond with 412 Precondition Failed. */
if (false === $this->cache->hasCurrentState($request, $content->etag(), $content->timestamp())) {
	throw new PreconditionFailedException("Content has been modified.", 412);
}

$body = $request->getParsedBody();
$content->data($body);
$this->spot->mapper("App\Content")->save($content);

/* Add Last-Modified and ETag headers to response. */
$response = $this->cache->withEtag($response, $content->etag());
$response = $this->cache->withLastModified($response, $content->timestamp());

$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($content, new ContentTransformer);
$data = $fractal->createData($resource)->toArray();
$data["status"] = "ok";
$data["message"] = "Content updated";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/contents/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["content.all", "content.update"])) {
		throw new ForbiddenException("Token not allowed to update contents.", 403);
	}

	/* Load existing content using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

/* PUT requires If-Unmodified-Since or If-Match request header to be present. */
if (false === $this->cache->hasStateValidator($request)) {
	throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
}

/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
/* someone has modified the content respond with 412 Precondition Failed. */
if (false === $this->cache->hasCurrentState($request, $content->etag(), $content->timestamp())) {
	throw new PreconditionFailedException("Content has been modified.", 412);
}

$body = $request->getParsedBody();

/* PUT request assumes full representation. If any of the properties is */
/* missing set them to default values by clearing the content object first. */
$content->clear();
$content->data($body);
$this->spot->mapper("App\Content")->save($content);

/* Add Last-Modified and ETag headers to response. */
$response = $this->cache->withEtag($response, $content->etag());
$response = $this->cache->withLastModified($response, $content->timestamp());

$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($content, new ContentTransformer);
$data = $fractal->createData($resource)->toArray();
$data["status"] = "ok";
$data["message"] = "Content updated";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/contents/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["content.all", "content.delete"])) {
		throw new ForbiddenException("Token not allowed to delete contents.", 403);
	}

	/* Load existing content using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

$this->spot->mapper("App\Content")->delete($content);

$data["status"] = "ok";
$data["message"] = "Content deleted";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
