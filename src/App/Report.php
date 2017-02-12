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
 
 class Report extends \Spot\Entity
 {
    protected static $table = "report";

    public static function fields()
    {
        return [
       

        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "reported_by_id" => ["type" => "integer", "unsigned" => true],
        "type" => ["type" => "string"],
        "timestamp" => ["type" => "datetime", "value" => new \DateTime()],
        "type_id" => ["type" => "integer", "unsigned" => true],
        "reason" => ["type" => "string"],
        "reported" => ["type" => "integer"],
        ];
    }

    public static function reports(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->id = Base62::encode(random_bytes(9));
            });

        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->timestamp = new \DateTime();
            });
    }
    public function timestamp()
    {
        return $this->timestamp->getTimestamp();
    }

    public function etag()
    {
        return md5($this->id . $this->timestamp());
    }

    public function clear()
    {
        $this->data([
            "id" => null,
            "reported_by_id" => null,
            "type" => null,
            // "timestamp" => null,
            "type_id" => null,
            "reason" => null,
            "reported" => null
            ]);
    }
}
