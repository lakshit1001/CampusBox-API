<?php

 

 namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;
 use App\Student;


 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class Skill extends \Spot\Entity
 {
    protected static $table = "skills";

    public static function fields()
    {
        return [
       

        "skill_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "name" => ["type" => "string"]
        ];
    }

    public static function skills(EventEmitter $emitter)
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
            ];
    }
}
