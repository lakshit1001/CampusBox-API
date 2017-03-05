<?php

 

$container = $app->getContainer();

$container["errorHandler"] = function ($container) {
    return new Slim\Handlers\ApiError($container["logger"]);
};
