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
 
 class ClassGroup extends \Spot\Entity
 {
    protected static $table = "class_groups";

    public static function fields()
    {
        return [
        

        "class_group_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "branch_id" => ["type" => "integer" , "unsigned" => true],
        "name" => ["type" => "string"],
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
            "class_group_id"=>null,
            "branch_id"=>null,
            "name"=>null

            ]);
    }
    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'Branch' => $mapper->belongsTo($entity, 'App\Branch', 'college_id'),
        'Students' => $mapper->hasMany($entity, 'App\Student', 'id')
        ];
    }

}
