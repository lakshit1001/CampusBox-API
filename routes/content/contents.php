<?php

use App\Content;
use App\ContentItems;
use App\ContentTags;
use App\ContentTransformer;
use App\ContentItemsTransformer;
use Exception\ForbiddenException;
use Exception\NotFoundException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/contentSorted", function ($request, $response, $arguments) {
	$user_college_id = $this->token->decoded->college_id;
	$username= $this->token->decoded->username;
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
        ORDER BY interestScore+interScore+followScore DESC,contents.timer 
    	LIMIT 3 OFFSET 0
        ;");

    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

});
$app->get("/contents[/{content_type_id}]", function ($request, $response, $arguments) {

	$limit = isset($_GET['limit']) ? $_GET['limit'] : 3;
	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;

	$test = $this->token->decoded->username;

	if(isset($arguments['content_type_id'])){
		$contents = $this->spot->mapper("App\Content")
		->all()
		->where(["content_type_id"=>$arguments['content_type_id']])
		->order(["timer" => "DESC"]);
	}else{

		$contents = $this->spot->mapper("App\Content")
		->all()
		->limit($limit, $offset)
		->order(["timer" => "DESC"]);
	}
	$offset += $limit;

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($contents, new ContentTransformer([ 'type' => 'get', 'username' => $test]));
	$data = $fractal->createData($resource)->toArray();
	
	$data['meta']['offset'] = $offset;
	$data['meta']['limit'] = $limit;

	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->get("/contentsDashboard", function ($request, $response, $arguments) {

	$test = $this->token->decoded->username;

	if(isset($arguments['content_type_id'])){
		$contents = $this->spot->mapper("App\Content")
		->all()
		->where(["content_type_id"=>$arguments['content_type_id']])
		->order(["timer" => "DESC"]);
	}else{

		$contents = $this->spot->mapper("App\Content")
		->all()
		->limit(6)
		->order(["timer" => "DESC"]);
	}

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($contents, new ContentTransformer([ 'type' => 'get', 'username' => $test]));
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->get("/contentsRandom", function ($request, $response, $arguments) {

	$test = $this->token->decoded->username;

	
		$contents = $this->spot->mapper("App\Content")
    ->query("SELECT * from contents ORDER BY RAND() limit 3"); 

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($contents, new ContentTransformer([ 'type' => 'get', 'username' => $test]));
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->get("/contentsTop[/{content_type_id}]", function ($request, $response, $arguments) {

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
	$resource = new Collection($contents, new ContentTransformer([ 'type' => 'get', 'username' => $test]));
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

	$test = $this->token->decoded->username;
	/* Load existing content using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"content_id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

/* Serialize the response data. */
$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($content, new ContentTransformer(['username' => $test, 'type' => 'get']));
$data = $fractal->createData($resource)->toArray();

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/addContent", function ($request, $response, $arguments) {
	$body = $request->getParsedBody();

	$content['created_by_username'] =  $this->token->decoded->username;
	$content['college_id'] =  $this->token->decoded->college_id;
	$content['title'] = $body['title'];
	$content['content_type_id'] = $body['type'];
	
	$newContent = new Content($content);
	$this->spot->mapper("App\Content")->save($newContent);

	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Item($newContent, new ContentTransformer);
	$data = $fractal->createData($resource)->toArray();
			//adding interests 

	for ($i=0; $i < count($body['items']); $i++) {
		$items['content_id'] = $data['data']['id'];
		$items['description'] = isset($body['items'][$i]['text'])?$body['items'][$i]['text']:"";
		$items['content_item_type'] = $body['items'][$i]['mediaType'];
		$items['image'] = isset($body['items'][$i]['image'])?$body['items'][$i]['image']:"";
		$items['embed_url'] = isset($body['items'][$i]['embedUrl'])?$body['items'][$i]['embedUrl']:"";
		$itemsElement = new ContentItems($items);
		$this->spot->mapper("App\ContentItems")->save($itemsElement);
	}
	for ($i=0; $i < count($body['tags']); $i++) {
		$tags['content_id'] = $data['data']['id'];
		$tags['name'] = $body['tags'][$i]['name'];
		$tagsElement = new ContentTags($tags);
		$this->spot->mapper("App\ContentTags")->save($tagsElement);
	}

	/* Serialize the response data. */
	$data["status"] = true;
	$data["message"] = 'Added Successfully';
	return $response->withStatus(201)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->patch("/content/{id}", function ($request, $response, $arguments) {

	// /* Check if token has needed scope. */
	// if (true === $this->token->hasScope(["event.all", "event.update"])) {
	// 	throw new ForbiddenException("Token not allowed to update events.", 403);
	// }

	/* Load existing event using provided id */
	if (false === $content = $this->spot->mapper("App\Content")->first([
		"content_id" => $arguments["id"],
		])) {
		throw new NotFoundException("Content not found.", 404);
};

/* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
// if (false === $this->cache->hasStateValidator($request)) {
// 	throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
// }

/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
/* someone has modified the event respond with 412 Precondition Failed. */
// if (false === $this->cache->hasCurrentState($request, $event->etag(), $event->timestamp())) {
// 	throw new PreconditionFailedException("Event has been modified.", 412);
// }

$body = $request->getParsedBody();
$content->data($body);
$this->spot->mapper("App\Content")->save($content);

// /* Add Last-Modified and ETag headers to response. */
// $response = $this->cache->withEtag($response, $event->etag());
// $response = $this->cache->withLastModified($response, $event->timestamp());

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