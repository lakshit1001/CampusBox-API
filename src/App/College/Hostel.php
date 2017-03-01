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

 use Spot\EntityInterface as Entity;
 use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class Hostel extends \Spot\Entity
 {
    protected static $table = "hostel";

    public static function fields()
    {
        return [


        "hostel_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "college_id" => ["type" => "integer" , "unsigned" => true,],
        "name" => ["type" => "string"],
        "gender" => ["type" => "string"],
        "lat" => ["type" => "float"],
        "long" => ["type" => "float"]       
        ];
    }

    public static function colleges(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->college_id = Base62::encode(random_bytes(9));
            });
    }

    public function clear()
    {
        $this->data([
         "hostel_id" => null,
         "college_id" => null,
         "name" => null,
         "gender" => null,
         "lat" => null,
         "long" => null
         ]);
    }
    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'College' => $mapper->belongsTo($entity, 'App\College', 'college_id'),
        ];
    }

}
