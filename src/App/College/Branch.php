<?php

 

 namespace App;

 use Spot\EntityInterface as Entity;
 use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class Branch extends \Spot\Entity
 {
    protected static $table = "branches";

    public static function fields()
    {
        return [


        "branch_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "branch_id" => ["type" => "integer" , "unsigned" => true,],
        "programme_id" => ["type" => "integer" , "unsigned" => true,]
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
            "branch_id"=>null,
             "programme_id"=>null,
             "name"=>null
            ]);
    }
    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'Programme' => $mapper->belongsTo($entity, 'App\Programme', 'college_id'),
        'ClassGroups' => $mapper->hasMany($entity, 'App\ClassGroup', 'id'),

        ];
    }

}
