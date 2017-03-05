<?php

 

namespace App;

use App\Report;
use League\Fractal;

class ReportTransformer extends Fractal\TransformerAbstract
{

    public function transform(Report $report)
    {
        return [
            "id" => (integer)$report->id?: 0 ,
            "reported_by_id" => (integer)$report->reported_by_id?: 0 ,
            "type" => (string)$report->type?: null ,
            "timestamp" =>$report->timestamp?: 0 ,
            "type_id" => (integer)$report->type_id?: 0 ,
            "reason" => (string)$report->reason?: null ,
            "reported" => (integer)$report->reported?: 0 ,

            "links"        => [
                "self" => "/reports/{$report->id}"
            ]
        ];
    }
}
