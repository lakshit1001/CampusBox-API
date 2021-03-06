<?php

 

 namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;
 use App\Student;


 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class StudentFollow extends \Spot\Entity
 {
    protected static $table = "followers";

    public static function fields()
    {
        return [
       

        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "followed_username" => ["type" => "string"],
        "follower_username" => ["type" => "string"],
        "timer" => ["type" => "datetime"]
        ];
    }

    public static function followed(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->id = Base62::encode(random_bytes(9));
            });
    }

    public function clear()
    {
        $this->data([
            ]);
    }
    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'Following' => $mapper->belongsTo($entity, 'App\Student', 'followed_username'),
            'Follower' => $mapper->belongsTo($entity, 'App\Student', 'follower_username')
            ];
    }
}
