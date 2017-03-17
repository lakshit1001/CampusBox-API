<?php

 

namespace App;

use App\CollegeUpdate;
use League\Fractal;

class CollegeUpdateTransformer extends Fractal\TransformerAbstract
{

    public function transform(CollegeUpdate $college_update)
    {
        return [
        "update_id" =>  $college_update->update_id?: 0,
        "college_id" =>  $college_update->college_id?: 0,
        "title" =>  $college_update->title?: 0,
        "message" =>  $college_update->message?: 0,
        "link" =>  $college_update->link?: 0,
        "timer" =>  $college_update->timer?: 0,
        ];
    }
}
