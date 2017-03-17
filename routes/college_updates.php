<?php

 

use App\CollegeUpdate;
use App\CollegeUpdateTransformer;

use Exception\ForbiddenException;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/college_updates", function ($request, $response, $arguments) {

   

    /* Use ETag and date from CollegeUpdate with most recent update. */
    $first = $this->spot->mapper("App\CollegeUpdate")
        ->all()
        ->where(["college_id" => $this->token->decoded->college_id])
        ->first();

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $colleges = $this->spot->mapper("App\CollegeUpdate")
        ->all()
        ->where(["college_id" => $this->token->decoded->college_id])
        ;

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($colleges, new CollegeUpdateTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
