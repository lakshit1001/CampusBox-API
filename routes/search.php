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

use App\Event;
use App\EventTransformer;
use Exception\ForbiddenException;
use Exception\NotFoundException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/search/{query}", function ($request, $response, $arguments) {

	/* Check if token has needed scope. */
	// if (true === $this->token->hasScope(["event.all", "event.list"])) {
	//     throw new ForbiddenException("Token not allowed to list events.", 403);
	// }else{

	// }

	$query =$arguments["query"];
	$table ="events";

	$events = $this->spot->mapper("App\Event")
		->query("SELECT *, MATCH (subtitle) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score,  MATCH (description) AGAINST ('".$query."*' IN BOOLEAN MODE) AS descriptionscore, MATCH (title) AGAINST ('".$query."*' IN BOOLEAN MODE) AS titlescore FROM ".$table." WHERE MATCH(title) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) or  MATCH(subtitle) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) or MATCH(description) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) or MATCH(title,subtitle) AGAINST('".$query."*' IN BOOLEAN MODE) OR title LIKE '%".$query."%'  OR subtitle LIKE '%".$query."%'  OR description LIKE '%".$query."%'  order by titlescore desc,descriptionscore desc, score desc limit 10" );
	/* Serialize the response data. */
	$fractal = new Manager();
	$fractal->setSerializer(new DataArraySerializer);
	if (isset($_GET['include'])) {
		$fractal->parseIncludes($_GET['include']);
	}
	$resource = new Collection($events, new EventTransformer(['student_id' => $this->token->decoded->student_id ]));
	$data = $fractal->createData($resource)->toArray();

	return $response->withStatus(200)
		->withHeader("Content-Type", "application/json")
		->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
