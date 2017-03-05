<?php

 

 namespace App;

 use Spot\EntityInterface as Entity;
 use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class ClassGroup extends \Spot\Entity
 {
    protected static $table = "event_images";

    public static function fields()
    {
        return [
        

        "event_image_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "event_id" => ["type" => "integer" , "unsigned" => true],
        "usl" => ["type" => "string"],
                "timer" => ["type" => "datetime", "value" => new \DateTime()],

        ];
    }

    public static function colleges(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->college_id = Base62::encode(random_bytes(9));
            });
        
        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->timestamp = new \DateTime();
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
