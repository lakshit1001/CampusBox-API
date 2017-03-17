<?php

 

date_default_timezone_set("UTC");
require __DIR__ . "/vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$app = new \Slim\App([
	"settings" => [
		"displayErrorDetails" => true,
	],
	'debug' => true,
]);

require __DIR__ . "/config/dependencies.php";
require __DIR__ . "/config/handlers.php";
require __DIR__ . "/config/middleware.php";

$app->get("/", function ($request, $response, $arguments) {
	print "Here be dragons";
});

require __DIR__ . "/routes/token.php";
require __DIR__ . "/routes/search.php";
require __DIR__ . "/routes/events/events.php";
require __DIR__ . "/routes/content/contents.php";
require __DIR__ . "/routes/reports.php";
require __DIR__ . "/routes/events/eventActions.php";
require __DIR__ . "/routes/content/contentActions.php";
require __DIR__ . "/routes/colleges.php";
require __DIR__ . "/routes/college_updates.php";
require __DIR__ . "/routes/skills.php";
require __DIR__ . "/routes/notifications.php";
require __DIR__ . "/routes/students.php";
require __DIR__ . "/routes/authentication/login.php";
require __DIR__ . "/routes/authentication/signup.php";

// require __DIR__ . "/routes/teachers.php";

$app->run();
