<?php



namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;

use Tuupola\Base62;

use Ramsey\Uuid\Uuid;
use Psr\Log\LogLevel;

class CollegeUpdate extends \Spot\Entity
{
    protected static $table = "college_updates";

    public static function fields()
    {
        return [


        "update_id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "college_id" => ["type" => "integer"],
        "title" => ["type" => "string"],
        "message" => ["type" => "string"],
        "link" => ["type" => "string"],
        "timer" => ["type" => "string"],
        ];
    }


    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
        'College' => $mapper->belongsTo($entity, 'App\College', 'college_id'),
        ];
    }

}
