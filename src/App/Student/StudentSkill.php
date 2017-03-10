<?php



namespace App;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter;
use App\Student;
use Tuupola\Base62;
use Ramsey\Uuid\Uuid;
use Psr\Log\LogLevel;

class StudentSkill extends \Spot\Entity
{
    protected static $table = "student_skills";

    public static function fields()
    {
        return [


        "id" => ["type" => "integer" , "unsigned" => true, "primary" => true, "autoincrement" => true],
        "username" => ["type" => "string"],
        "skill_name" => ["type" => "string"]
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
        'Skill' => $mapper->belongsTo($entity, 'App\Student', 'username')
        ];
    }
}
