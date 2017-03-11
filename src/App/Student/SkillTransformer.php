<?php

 

namespace App;

use App\StudentSkill;
use League\Fractal;

class SkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(StudentSkill $skill)
    {
        return [
            "name" => (string)$skill->skill_name?: null 
           
        ];
    }
}
