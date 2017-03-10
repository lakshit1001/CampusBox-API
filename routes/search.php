<?php



use App\Event;
use App\EventTransformer;
use App\ContentMiniTransformer;
use App\StudentMiniTransformer;
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

    $query =isset($arguments["query"])?isset($arguments["query"]):" ";

    $events = $this->spot->mapper("App\Event")
    ->query("SELECT *, MATCH (title) AGAINST ".
        "('".$query."*' IN BOOLEAN MODE) AS score1,". 
        "MATCH (subtitle) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score2,".
        "MATCH (description) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score3 ".
        "FROM events WHERE MATCH(title) ".
        "AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
        "or  MATCH(subtitle) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION)".
        "or MATCH(description) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION)".
        "or MATCH(title) AGAINST('".$query."*' IN BOOLEAN MODE) ".
        "OR MATCH(subtitle) AGAINST('".$query."*' IN BOOLEAN MODE) ".
            "OR title LIKE '%".$query."%'  
            OR subtitle LIKE '%".$query."%'  ".
            "OR description LIKE '%".$query."%'  ".
            "order by score1 desc,score2 desc, score3 desc limit 4" );


    $content = $this->spot->mapper("App\Content")
    ->query("SELECT *, 
        CASE WHEN followers.followed_username IS NULL THEN 0 ELSE 8 END AS score,
        CASE WHEN content_appreciates.content_id IS NULL THEN 0 ELSE 1 END AS score1
        FROM contents
        LEFT JOIN followers
        ON contents.created_by_username = followers.followed_username
        LEFT JOIN content_appreciates
        ON contents.content_id = content_appreciates.content_id
        GROUP BY contents.content_id
        ORDER BY score1 DESC");
    



    $content2 = $this->spot->mapper("App\Content")
    ->query("SELECT *, MATCH (title) AGAINST ".
        "('".$query."*' IN BOOLEAN MODE) AS score1 ". 
        "FROM contents WHERE MATCH(title) ".
        "AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
        "or MATCH(title) AGAINST('".$query."*' IN BOOLEAN MODE) ".
        "OR title LIKE '%".$query."%'  ".
        "order by score1 desc limit 4" );

    $students = $this->spot->mapper("App\Student")
    ->query("SELECT *, MATCH (name) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score1,".
     "MATCH (username) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score2,".
     "MATCH (about) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score3 ".
     "FROM students WHERE MATCH(name) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
     "or  MATCH(username) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
     "or MATCH(about) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
     "or MATCH(name) AGAINST('".$query."*' IN BOOLEAN MODE) ".
     "OR MATCH(username) AGAINST('".$query."*' IN BOOLEAN MODE) ".
     "OR name LIKE '%".$query."%'  ".
     "OR username LIKE '%".$query."%'  ".
     "OR about LIKE '%".$query."%'  ".
     "order by score1 desc,score2 desc, score3 desc limit 4" );
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    if (isset($_GET['include'])) {
        $fractal->parseIncludes($_GET['include']);
    }
    $resource1 = new Collection($events, new EventTransformer(['username' => '1' ]));
    $resource2 = new Collection($students, new StudentMiniTransformer(['username' => '1' ]));
    $resource3 = new Collection($content, new ContentMiniTransformer(['username' => '1' ]));
    
    $arrs = array();
    $arrs[1] = $fractal->createData($resource1)->toArray();
    $arrs[0] = $fractal->createData($resource2)->toArray();
    $arrs[2] = $fractal->createData($resource3)->toArray();

    $list = array();

    foreach($arrs as $arr) {
        if(is_array($arr)) {
            $list = array_merge($list, (array)$arr);
        }
    }
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
