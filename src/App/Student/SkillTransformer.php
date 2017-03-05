<?php

 

namespace App;

use App\StudentSkill;
use League\Fractal;

class SkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(StudentSkill $skill)
    {
        return [
            "id" => (integer)$skill->skill_id?: 0 ,
            "name" => (string)$skill->skill_name?: null ,
            "links"        => [
                "self" => "/reports/{$skill->skill_id}"
            ]
        ];
    }
}
