<?php

 

namespace App;

use App\StudentSkill;
use League\Fractal;

class StudentSkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(StudentSkill $skill)
    {
        return [
            "id" => (integer)$skill->id?: 0 ,
            "username" => (string)$skill->username?: null,
            "skill_name" => (string)$skill->skill_name?: null 
        ];
    }
}
