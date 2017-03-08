<?php

 

 namespace App;

 use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
 use Spot\EventEmitter;

 use Tuupola\Base62;

 use Ramsey\Uuid\Uuid;
 use Psr\Log\LogLevel;
 
 class EventReport extends \Spot\Entity
 {
    protected static $table = "report";

    public static function fields()
    {
        return [
       

        "event_report_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "username" => ["type" => "string"],
        "type" => ["type" => "string"],
        "reason" => ["type" => "string"],
        "timer" => ["type" => "datetime", "value" => new \DateTime()],
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
            "type_id" => null,
            "reason" => null,
            "reported" => null
            ]);
    }
}
