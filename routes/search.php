<?php



use App\Event;
use App\EventTransformer;
use App\Content;
use App\ContentTransformer;
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

$app->get("/search/students/{username}", function ($request, $response, $arguments) {

  /* Load existing student using provided id */
    // if (false === $student = $this->spot->mapper("App\Student")->first([
    //     "username" => '%'.$arguments["username"].'%'
    // ])) {
    //     throw new NotFoundException("Student not found.", 404);
    // };
    // /* If-Modified-Since and If-None-Match request header handling. */
  /* Heads up! Apache removes previously set Last-Modified header */
  /* from 304 Not Modified responses. */
  if ($this->cache->isNotModified($request, $response)) {
    return $response->withStatus(304);
  }

  $students = $this->spot->mapper("App\Student")->query('
      SELECT * FROM students
      WHERE username LIKE "%'.$arguments['username'].'%"
    ');

  if(isset($students) ){
    /* Serialize the response data. */
    $fractal = new Manager();

    $fractal->setSerializer(new DataArraySerializer);

    $resource = new Collection($students, new StudentMiniTransformer);
    $data = $fractal->createData($resource)->toArray();
  }
  return $response->withStatus(200)
  ->withHeader("Content-Type", "appliaction/json")
  ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
$app->get("/search/events/{username}", function ($request, $response, $arguments) {

  /* Load existing student using provided id */
    // if (false === $student = $this->spot->mapper("App\Student")->first([
    //     "username" => '%'.$arguments["username"].'%'
    // ])) {
    //     throw new NotFoundException("Student not found.", 404);
    // };
    // /* If-Modified-Since and If-None-Match request header handling. */
  /* Heads up! Apache removes previously set Last-Modified header */
  /* from 304 Not Modified responses. */
  if ($this->cache->isNotModified($request, $response)) {
    return $response->withStatus(304);
  }

  $events = $this->spot->mapper("App\Event")->query('
      SELECT * FROM events
      WHERE title LIKE "%'.$arguments['username'].'%"
    ');

  if(isset($events) ){
    /* Serialize the response data. */
    $fractal = new Manager();

    $fractal->setSerializer(new DataArraySerializer);

    $resource = new Collection($events, new EventTransformer);
    $data = $fractal->createData($resource)->toArray();
  }
  return $response->withStatus(200)
  ->withHeader("Content-Type", "appliaction/json")
  ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/search/creativity/{username}", function ($request, $response, $arguments) {

  /* Load existing student using provided id */
    // if (false === $student = $this->spot->mapper("App\Student")->first([
    //     "username" => '%'.$arguments["username"].'%'
    // ])) {
    //     throw new NotFoundException("Student not found.", 404);
    // };
    // /* If-Modified-Since and If-None-Match request header handling. */
  /* Heads up! Apache removes previously set Last-Modified header */
  /* from 304 Not Modified responses. */
  if ($this->cache->isNotModified($request, $response)) {
    return $response->withStatus(304);
  }

  $creativity = $this->spot->mapper("App\Content")->query('
      SELECT * FROM contents
      WHERE title LIKE "%'.$arguments['username'].'%"
    ');

  if(isset($creativity) ){
    /* Serialize the response data. */
    $fractal = new Manager();

    $fractal->setSerializer(new DataArraySerializer);

    $resource = new Collection($creativity, new ContentTransformer);
    $data = $fractal->createData($resource)->toArray();
  }
  return $response->withStatus(200)
  ->withHeader("Content-Type", "appliaction/json")
  ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/search/{query}", function ($request, $response, $arguments) {

  /* Check if token has needed scope. */
    // if (true === $this->token->hasScope(["event.all", "event.list"])) {
    //     throw new ForbiddenException("Token not allowed to list events.", 403);
    // }else{

    // }

  $query =isset($arguments["query"])?isset($arguments["query"]):" ";


  // $events = $this->spot->mapper("App\Event")
  //   ->query("SELECT *, MATCH (title) AGAINST ".
  //       "('".$query."*' IN BOOLEAN MODE) AS score1,". 
  //       "MATCH (subtitle) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score2,".
  //       "MATCH (description) AGAINST ('".$query."*' IN BOOLEAN MODE) AS score3 ".
  //       "FROM events".
  //       " WHERE MATCH(title) ".
  //       "AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
  //       "or  MATCH(subtitle) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION)".
  //       "or MATCH(description) AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION)".
  //       "or MATCH(title) AGAINST('".$query."*' IN BOOLEAN MODE) ".
  //       "OR MATCH(subtitle) AGAINST('".$query."*' IN BOOLEAN MODE) ".
  //           "OR title LIKE '%".$query."%'  
  //           OR subtitle LIKE '%".$query."%'  ".
  //           "OR description LIKE '%".$query."%'  ".
  //           "order by score1 desc,score2 desc, score3 desc,events.to_date desc limit 3" );


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
        ORDER BY score1 DESC ,contents.timer desc limit 2");
    



  //   $content2 = $this->spot->mapper("App\Content")
  //   ->query("SELECT *, MATCH (title) AGAINST ".
  //       "('".$query."*' IN BOOLEAN MODE) AS score1 ". 
  //       "FROM contents WHERE MATCH(title) ".
  //       "AGAINST('".$query."*' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ".
  //       "or MATCH(title) AGAINST('".$query."*' IN BOOLEAN MODE) ".
  //       "OR title LIKE '%".$query."%'  ".
  //       "order by score1 desc,  contents.timer desc limit 3" );

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
     "order by score1 desc,score2 desc, score3 desc limit 2" );


    $events = $this->spot->mapper("App\Event")
    ->query(("SELECT * from events where title LIKE '%".$query."%' limit 2" ));
  // $students = $this->spot->mapper("App\Student")
  //   ->query(("SELECT * from students where name LIKE '%".$query."%' limit 3" ));
  //   $content = $this->spot->mapper("App\Content")
  //   ->query(("SELECT * from contents where title LIKE '%".$query."%' limit 3" ));
    
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    if (isset($_GET['include'])) {
      $fractal->parseIncludes($_GET['include']);
    }
    $resource1 = new Collection($students, new StudentMiniTransformer(['username' => '1' ]));
    $resource2 = new Collection($events, new EventTransformer(['username' => '1' ]));
    $resource3 = new Collection($content, new ContentMiniTransformer(['username' => '1' ]));
    
    $arrs = array();
    $arrs[0] = $fractal->createData($resource1)->toArray();
    $arrs[1] = $fractal->createData($resource2)->toArray();
    $arrs[2] = $fractal->createData($resource3)->toArray();
    $arrs[0] = array_filter($arrs[0]);
    $arrs[1] = array_filter($arrs[1]);
    $arrs[2] = array_filter($arrs[2]);
    $list =[];
    foreach($arrs as $arr) {
      foreach($arr as $item) {
        foreach($item as $bitch) {
          if(isset($bitch['photo'])){
            $bitch['type'] = "student";
            $bitch['image'] = $bitch['photo'];
          }else if(isset($bitch['content'])){
            $bitch['type'] = "content";
          }else{
            $bitch['type'] = "event";
          }
          $list[] = $bitch;
        }
      }
    }
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($list, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  });
