<?php

 

 namespace App;

 use Spot\EntityInterface as Entity;
 use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class ContentType extends \Spot\Entity
 {
    protected static $table = "content_types";

    public static function fields()
    {
        return [


        "content_type_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "name" => ["type" => "string"],
        "default_view_type" => ["type" => "integer","required" => true],
        "has_multiple_view_types" => ["type" => "boolean"]
        ];
    }

    public static function contents(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->content_id = Base62::encode(random_bytes(9));
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
            'Contents' => $mapper->hasMany($entity, 'App\Content', 'content_type_id')
        ];
    }
}
