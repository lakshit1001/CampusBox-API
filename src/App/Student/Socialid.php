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
 
 class Socialid extends \Spot\Entity
 {
    protected static $table = "social_ids";

    public static function fields()
    {
        return [


        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "student_id" => ["type" => "integer"],
        "facebook" => ["type" => "string"],
        "instagram" => ["type" => "string"],
        "github" => ["type" => "string"],
        "behance" => ["type" => "string"],
        "soundcloud" => ["type" => "string"],
        "linkedin" => ["type" => "string"],
        "other" => ["type" => "string"]
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
            "student_id" => 0,
            "facebook" => null,
            "instagram" => null,
            "github" => null,
            "behance" => null,
            "soundcloud" => null,
            "linkedin" => null,
            "other" => null,
            ]);
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'Socialid' => $mapper->belongsTo($entity, 'App\Student', 'student_id'),
        ];
    }
}
