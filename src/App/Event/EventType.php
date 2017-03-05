<?php

 

 namespace App;

 use Spot\EntityInterface as Entity;
 use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class EventType extends \Spot\Entity
 {
    protected static $table = "event_types";

    public static function fields()
    {
        return [


        "event_type_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "name" => ["type" => "string"]
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
            'Type' => $mapper->hasMany($entity, 'App\Event', 'event_type_id')
        ];
    }
}
