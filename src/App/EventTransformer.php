<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use App\Event;
use League\Fractal;

class EventTransformer extends Fractal\TransformerAbstract
{

    public function transform(Event $event)
    {
        return [
            "id" => (integer)$event->id?: 0 ,
            "college_id" => (integer)$event->college_id?: 0 ,
            "created_by_id" => (integer)$event->created_by_id?: 0 ,
            "title" => (string)$event->title?: null ,
            "subtitle" => (string)$event->subtitle?: null ,
            "description" => (string)$event->description?: null ,
            "contactperson1" => (integer)$event->contactperson1?: 0 ,
            "contactperson2" => (integer)$event->contactperson2?: 0 ,
            "venue" => (string)$event->venue?: null ,
            "inter" => (integer)$event->inter?: 0 ,
            "time_created" =>$event->time_created?: 0 ,
            "type" => (string)$event->type?: null ,
            "price" => (integer)$event->price?: 0 ,
            "created_by" => (string)$event->Owner['name']?: null,
            "participants" => $event->Participants[0],

            "links"        => [
                "self" => "/events/{$event->id}"
            ]
        ];
    }
}
