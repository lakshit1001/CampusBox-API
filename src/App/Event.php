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
 
 class Event extends \Spot\Entity
 {
    protected static $table = "events";

    public static function fields()
    {
        return [


        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "college_id" => ["type" => "integer", "unsigned" => true],
        "created_by_id" => ["type" => "integer", "required" => true],
        "title" => ["type" => "string", "required" => true],
        "subtitle" => ["type" => "string" ],
        "description" => ["type" => "string", "required" => true],
        "contactperson1" => ["type" => "integer"],
        "contactperson2" => ["type" => "integer"],
        "venue" => ["type" => "string", "required" => true],
        "inter" => ["type" => "integer", "required" => true, "default" => "0"],
        "time_created" => ["type" => "datetime", "value" => new \DateTime()],
        "type" => ["type" => "string"],
        "price" => ["type" => "integer"]

        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->id = Base62::encode(random_bytes(9));
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
            "id" => null,
            "college_id" => null,
            "created_by_id" => null,
            "title" => null,
            "subtitle" => null,
            "description" => null,
            "contactperson1" => null,
            "contactperson2" => null,
            "venue" => null,
            "inter" => null,
            "time_created" => null,
            "type" => null,
            "price" => null
            ]);
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'Images' => $mapper->hasMany($entity, 'Entity\EventImage', 'post_id'),
        'Owner' => $mapper->belongsTo($entity, 'Entity\Student', 'user_id')
        'Tags' => $mapper->hasManyThrough($entity, 'Entity\Tag', 'Entity\ContentCategory', 'tag_id', 'post_id'),
        'Likes' => $mapper->hasManyThrough($entity, 'Entity\Student', 'Entity\EventLikes', 'tag_id', 'post_id'),
        'Bookmarked' => $mapper->hasManyThrough($entity, 'Entity\Student', 'Entity\EventBookmarks', 'tag_id', 'post_id'),
        ];
    }
}
