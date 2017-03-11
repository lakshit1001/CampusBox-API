<?php

 

namespace App;

use App\StudentSkill;
use League\Fractal;

class SkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(Skill $skill)
    {
        return [
            "skill_id" => (integer)$skill->skill_id?: 0 ,
            "name" => (string)$skill->name?: null 
        ];
    }
}
