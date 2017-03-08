<?php

 

 use App\StudentFollow;
 use App\StudentFollowTransformer;
 use Exception\ForbiddenException;
 use Exception\NotFoundException;
 use Exception\PreconditionFailedException;
 use Exception\PreconditionRequiredException;
 use League\Fractal\Manager;
 use League\Fractal\Resource\Collection;
 use League\Fractal\Resource\Item;
 use League\Fractal\Serializer\DataArraySerializer;

 $app->get("/notifications/{username}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    // if (true === $this->token->hasScope(["event.all", "event.list"])) {
    //     throw new ForbiddenException("Token not allowed to list events.", 403);
    // }else{

    // }

    $username =$arguments["username"];

    $follows = $this->spot->mapper("App\StudentFollow")
        ->all()
        ->where(["followed_id" => $username])
        ->order(["timer" => "DESC"]);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    if (isset($_GET['include'])) {
        $fractal->parseIncludes($_GET['include']);
    }
    $resource1 = new Collection($follows, new StudentFollowTransformer(['username' => $username ]));
   // $resource2 = new Collection($students, new StudentMiniTransformer(['username' => '1' ]));
   // $resource3 = new Collection($content, new ContentMiniTransformer(['username' => '1' ]));
    
    $arrs = array();
    $arrs[0] = $fractal->createData($resource1)->toArray();
   // $arrs[1] = $fractal->createData($resource2)->toArray();
   // $arrs[2] = $fractal->createData($resource3)->toArray();

$list = array();

foreach($arrs as $arr) {
    if(is_array($arr)) {
        $list = array_merge($list, (array)$arr);
    }
}
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($arrs[0], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });
