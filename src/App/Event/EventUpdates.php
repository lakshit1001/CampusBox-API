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
 
 class EventUpdates extends \Spot\Entity
 {
    protected static $table = "event_updates";

    public static function fields()
    {
        return [


        "event_update_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "event_id" => ["type" => "integer", "unsigned" => true],
        "title" => ["type" => "string"],
        "message" => ["type" => "string"],
        "color" => ["type" => "string" ],
        "society_id" => ["type" => "integer"],
        "student_id" => ["type" => "integer"],

        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->event_id = Base62::encode(random_bytes(9));
            });

        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->time_created = new \DateTime();
            });
    }
    public function timestamp()
    {
        return $this->time_created->getTimestamp();
    }

    public function etag()
    {
        return md5($this->id . $this->timestamp());
    }

    public function clear()
    {
        $this->data([
            ]);
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'Event' => $mapper->belongsTo($entity, 'App\Event', 'event_id'),
        'Owner' => $mapper->belongsTo($entity, 'App\Student', 'student_id'),
        // 'Society' => $mapper->belongsTo($entity, 'App\Society', 'society_id')
        ];
    }
}
