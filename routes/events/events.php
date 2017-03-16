<?php



use App\Event;
use App\EventTags;
use App\EventTransformer;
use App\EventRsvp;
use App\EventRsvpTransformer;
use App\EventBookmarks;
use App\EventBookmarksTransformer;
use Exception\ForbiddenException;
use Exception\NotFoundException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;


$app->get("/events", function ($request, $response, $arguments) {

	$currentCursor = 0;
	$previousCursor = 0;
	$limit = 20;
	$test = $this->token->decoded->username;

	/* Use ETag and date from Event with most recent update. */
	$first = $this->spot->mapper("App\Event")
	->all()
	->order(["time_created" => "DESC"])
	->first();

	/* Add Last-Modified and ETag headers to response when atleast on event exists. */
	// if ($first) {
	// 	$response = $this->cache->withEtag($response, $first->etag());
	// 	$response = $this->cache->withLastModified($response, $first->timestamp());
	// }

	/* If-Modified-Since and If-None-Match request header handling. */
	/* Heads up! Apache removes previously set Last-Modified header */
	/* from 304 Not Modified responses. */
	if ($this->cache->isNotModified($request, $response)) {
		return $response->withStatus(304);
	}

	if(0){

		$events = $this->spot->mapper("App\Event")
		->where(['event_id >' => $currentCursor])
		->limit($limit)
		->order(["time_created" => "DESC"]);
	} else {
		$events = $this->spot->mapper("App\Event")
		->all()
		->order(["time_created" => "DESC"]);
	}

    // $newCursor = $events->last()->id;
    // $cursor = new Cursor($currentCursor, $previousCursor, $newCursor, $events->count());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($events, new EventTransformer(['username' => $test, 'type' => 'get']));
	$data = $fractal->createData($resource)->toArray();
	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/eventsDashboard", function ($request, $response, $arguments) {

	$currentCursor = 3;
	$previousCursor = 2;
	$limit = 5;
	$test = $this->token->decoded->username;

	/* Use ETag and date from Event with most recent update. */
	$first = $this->spot->mapper("App\Event")
	->all()
	->order(["time_created" => "DESC"])
	->first();

	/* If-Modified-Since and If-None-Match request header handling. */
	/* Heads up! Apache removes previously set Last-Modified header */
	/* from 304 Not Modified responses. */
	if ($this->cache->isNotModified($request, $response)) {
		return $response->withStatus(304);
	}

	if($currentCursor){

		$events = $this->spot->mapper("App\Event")
		->where(['event_id >' => $currentCursor])
		->limit($limit)
		->order(["time_created" => "DESC"]);
	} else {
		$events = $this->spot->mapper("App\Event")
		->limit(5)
		->get();
	}

    // $newCursor = $events->last()->id;
    // $cursor = new Cursor($currentCursor, $previousCursor, $newCursor, $events->count());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($events, new EventTransformer(['username' => $test, 'type' => 'get']));
	$data = $fractal->createData($resource)->toArray();
	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/event/{event_id}", function ($request, $response, $arguments) {

	$currentCursor = 3;
	$previousCursor = 2;
	$limit = 20;
	$test = $this->token->decoded->username;

	/* Use ETag and date from Event with most recent update. */
	$first = $this->spot->mapper("App\Event")
	->all()
	->order(["time_created" => "DESC"])
	->first();

	/* Add Last-Modified and ETag headers to response when atleast on event exists. */
	// if ($first) {
	// 	$response = $this->cache->withEtag($response, $first->etag());
	// 	$response = $this->cache->withLastModified($response, $first->timestamp());
	// }

	/* If-Modified-Since and If-None-Match request header handling. */
	/* Heads up! Apache removes previously set Last-Modified header */
	/* from 304 Not Modified responses. */
	if ($this->cache->isNotModified($request, $response)) {
		return $response->withStatus(304);
	}
	$events = $this->spot->mapper("App\Event")
	->where(['event_id' => $arguments['event_id']])
	->limit(1);
	

    // $newCursor = $events->last()->id;
    // $cursor = new Cursor($currentCursor, $previousCursor, $newCursor, $events->count());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($events, new EventTransformer(['username' => $test, 'type' => 'get']));
	$data = $fractal->createData($resource)->toArray();
	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->post("/eventsFilter", function ($request, $response, $arguments) {
	$body = $request->getParsedBody();

	$currentCursor = 3;
	$previousCursor = 2;
	$limit = 20;
	$test = $this->token->decoded->username;

	/* Use ETag and date from Event with most recent update. */
	$first = $this->spot->mapper("App\Event")
	->all()
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

	if($currentCursor){

		$events = $this->spot->mapper("App\Event")
		->where($body)
		->limit($limit)
		->order(["time_created" => "DESC"]);
	} else {
		$events = $this->spot->mapper("App\Event")
		->limit($limit)
		->get();
	}

    // $newCursor = $events->last()->id;
    // $cursor = new Cursor($currentCursor, $previousCursor, $newCursor, $events->count());

	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($events, new EventTransformer(['username' => $test, 'type' => 'get']));
	$data = $fractal->createData($resource)->toArray();
	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/addEvent", function ($request, $response, $arguments) {
	$body = $request->getParsedBody();

	$event['college_id'] =  $this->token->decoded->college_id;
	$event['created_by_username'] =  $this->token->decoded->username;
	$event['title'] = $body['event']['title'];
	$event['subtitle'] = $body['event']['subtitle'];
	$event['image'] = $body['event']['croppedDataUrl'];
	$event['price'] = $body['event']['price'];
	$event['description'] = $body['event']['description'];
	//$event['contactperson1'] = $body['event']['contactperson1'];
	$event['venue'] = $body['event']['venue'];
	$event['audience'] = $body['event']['audience'];
	$event['event_type_id'] = (int)$body['event']['type'];
	$event['event_category_id'] = (int)$body['event']['category'];
	$event['link'] = $body['event']['link'];
	$event['organiser_name'] = $body['event']['organiserName'];
	$event['organiser_phone'] = (int)$body['event']['organiserPhone'];
	$event['organiser_link'] = $body['event']['organiserLink'];
	
	$event['to_date'] = $body['event']['toDate'];
	$event['to_time'] = $body['event']['toTime'];
	$event['to_period'] = $body['event']['toPeriod']=="am"?0:1;
	$event['from_date'] = $body['event']['fromDate'];
	$event['from_time'] = $body['event']['fromTime'];
	$event['from_period'] = $body['event']['fromPeriod']=="am"?0:1;

	$newEvent = new Event($event);
	$this->spot->mapper("App\Event")->save($newEvent);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($newEvent, new EventTransformer);
    $data = $fractal->createData($resource)->toArray();

	for ($i=0; $i < count($body['tags']); $i++) {
		$tags['event_id'] = $data['data']['id'];
		$tags['name'] = $body['tags'][$i]['name'];
		$intrest = new EventTags($tags);
		$this->spot->mapper("App\EventTags")->save($intrest);
	}

	/* Serialize the response data. */
	$data["status"] = 'Registered Successfully';
	return $response->withStatus(201)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/eventParticipants/{id}", function ($request, $response, $arguments) {
	$participants = $this->spot->mapper("App\EventRsvp")->where(["event_id" => $arguments['id']]);
	
	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	$resource = new Collection($participants, new EventRsvpTransformer);
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.update"])) {
		throw new ForbiddenException("Token not allowed to update events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Event not found.", 404);
};

/* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
if (false === $this->cache->hasStateValidator($request)) {
	throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
}

/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
/* someone has modified the event respond with 412 Precondition Failed. */
if (false === $this->cache->hasCurrentState($request, $event->etag(), $event->timestamp())) {
	throw new PreconditionFailedException("Event has been modified.", 412);
}

$body = $request->getParsedBody();
$event->data($body);
$this->spot->mapper("App\Event")->save($event);

/* Add Last-Modified and ETag headers to response. */
$response = $this->cache->withEtag($response, $event->etag());
$response = $this->cache->withLastModified($response, $event->timestamp());

$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($event, new EventTransformer);
$data = $fractal->createData($resource)->toArray();
$data["status"] = "ok";
$data["message"] = "Event updated";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.update"])) {
		throw new ForbiddenException("Token not allowed to update events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Event not found.", 404);
};

/* PUT requires If-Unmodified-Since or If-Match request header to be present. */
if (false === $this->cache->hasStateValidator($request)) {
	throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
}

/* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
/* someone has modified the event respond with 412 Precondition Failed. */
if (false === $this->cache->hasCurrentState($request, $event->etag(), $event->timestamp())) {
	throw new PreconditionFailedException("Event has been modified.", 412);
}

$body = $request->getParsedBody();

/* PUT request assumes full representation. If any of the properties is */
/* missing set them to default values by clearing the event object first. */
$event->clear();
$event->data($body);
$this->spot->mapper("App\Event")->save($event);

/* Add Last-Modified and ETag headers to response. */
$response = $this->cache->withEtag($response, $event->etag());
$response = $this->cache->withLastModified($response, $event->timestamp());

$fractal = new Manager();
$fractal->setSerializer(new DataArraySerializer);
$resource = new Item($event, new EventTransformer);
$data = $fractal->createData($resource)->toArray();
$data["status"] = "ok";
$data["message"] = "Event updated";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/events/{id}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	if (true === $this->token->hasScope(["event.all", "event.delete"])) {
		throw new ForbiddenException("Token not allowed to delete events.", 403);
	}

	/* Load existing event using provided id */
	if (false === $event = $this->spot->mapper("App\Event")->first([
		"id" => $arguments["id"],
		])) {
		throw new NotFoundException("Event not found.", 404);
};

$this->spot->mapper("App\Event")->delete($event);

$data["status"] = "ok";
$data["message"] = "Event deleted";

return $response->withStatus(200)
->withHeader("Content-Type", "application/json")
->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
