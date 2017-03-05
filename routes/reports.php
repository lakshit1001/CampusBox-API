<?php

 

use App\Report;
use App\ReportTransformer;

use Exception\NotFoundException;
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/reports", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.list"])) {
        throw new ForbiddenException("Token not allowed to list reports.", 403);
    }else{
       
    }

    /* Use ETag and date from Report with most recent update. */
    $first = $this->spot->mapper("App\Report")
        ->all()
        ->order(["timestamp" => "DESC"])
        ->first();

    /* Add Last-Modified and ETag headers to response when atleast on report exists. */
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

    $reports = $this->spot->mapper("App\Report")
        ->all()
        ->order(["timestamp" => "DESC"]);

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($reports, new ReportTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/reports", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.create"])) {
        throw new ForbiddenException("Token not allowed to create reports.", 403);
    }

    $body = $request->getParsedBody();

    $report = new Report($body);
    $this->spot->mapper("App\Report")->save($report);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $report->etag());
    $response = $this->cache->withLastModified($response, $report->timestamp());

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($report, new ReportTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New report created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/reports/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.read"])) {
        throw new ForbiddenException("Token not allowed to list reports.", 403);
    }

    /* Load existing report using provided id */
    if (false === $report = $this->spot->mapper("App\Report")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Report not found.", 404);
    };

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $report->etag());
    $response = $this->cache->withLastModified($response, $report->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($report, new ReportTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "appliaction/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/reports/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.update"])) {
        throw new ForbiddenException("Token not allowed to update reports.", 403);
    }

    /* Load existing report using provided id */
    if (false === $report = $this->spot->mapper("App\Report")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Report not found.", 404);
    };

    /* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the report respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $report->etag(), $report->timestamp())) {
        throw new PreconditionFailedException("Report has been modified.", 412);
    }

    $body = $request->getParsedBody();
    $report->data($body);
    $this->spot->mapper("App\Report")->save($report);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $report->etag());
    $response = $this->cache->withLastModified($response, $report->timestamp());

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($report, new ReportTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Report updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/reports/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.update"])) {
        throw new ForbiddenException("Token not allowed to update reports.", 403);
    }

    /* Load existing report using provided id */
    if (false === $report = $this->spot->mapper("App\Report")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Report not found.", 404);
    };

    /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the report respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $report->etag(), $report->timestamp())) {
        throw new PreconditionFailedException("Report has been modified.", 412);
    }

    $body = $request->getParsedBody();

    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the report object first. */
    $report->clear();
    $report->data($body);
    $this->spot->mapper("App\Report")->save($report);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $report->etag());
    $response = $this->cache->withLastModified($response, $report->timestamp());

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($report, new ReportTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Report updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/reports/{id}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (true === $this->token->hasScope(["report.all", "report.delete"])) {
        throw new ForbiddenException("Token not allowed to delete reports.", 403);
    }

    /* Load existing report using provided id */
    if (false === $report = $this->spot->mapper("App\Report")->first([
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Report not found.", 404);
    };

    $this->spot->mapper("App\Report")->delete($report);

    $data["status"] = "ok";
    $data["message"] = "Report deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
