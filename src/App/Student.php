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

 use Spot\EntityInterface;
 use Spot\MapperInterface;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class Student extends \Spot\Entity
 {
    protected static $table = "students";

    public static function fields()
    {
        return [
       

        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "college_id" => ["type" => "integer"],
        "name" => ["type" => "string"],
        "username" => ["type" => "string"],
        "roll_number" => ["type" => "integer"],
        "email" => ["type" => "string"],
        "phone" => ["type" => "integer"],
        "photo" => ["type" => "string"],
        "hostelid" => ["type" => "integer"],
        "room_number" => ["type" => "string"],
        "home_city" => ["type" => "string"],
        "grad_id" => ["type" => "integer"],
        "branch_id" => ["type" => "integer"],
        "year" => ["type" => "string"],
        "class_id" => ["type" => "integer"],
        "passout_year" => ["type" => "integer"],
        "age" => ["type" => "integer"],
        "gender" => ["type" => "string"]
        ];
    }

    public static function students(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->id = Base62::encode(random_bytes(9));
            });
    }

    public function clear()
    {
        $this->data([
            "id" => 0,
            "college_id" => null,
            "name" => null,
            "username" => null,
            "roll_number" => null,
            "email" => null,
            "phone" => null,
            "photo" => null,
            "hostelid" => null,
            "room_number" => null,
            "home_city" => null,
            "grad_id" => null,
            "branch_id" => null,
            "year" => null,
            "class_id" => null,
            "passout_year" => null,
            "age" => null,
            "gender" => null
            ]);
    }
}
