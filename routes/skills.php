<?php

 

use App\Skill;
use App\StudentSkill;
use App\SkillTransformer;
use App\StudentSkillTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/skills", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
 //   if (true === $this->token->hasScope(["skill.all", "skill.list"])) {
   //     throw new ForbiddenException("Token not allowed to list skills.", 403);
   // }else{
       
   // }

    /* Use ETag and date from Skill with most recent update. */
    $first = $this->spot->mapper("App\StudentSkill")
        ->all()
        ->first();

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $skills = $this->spot->mapper("App\StudentSkill")
        ->all();

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($skills, new SkillTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/skills/{username}", function ($request, $response, $arguments) {
    $skill = $this->spot->mapper("App\StudentSkill")->where([
        "username" => $arguments["username"]]);
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($skill, new StudentSkillTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "appliaction/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});