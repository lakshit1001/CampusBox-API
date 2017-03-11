<?php

 

namespace App;

use App\Skill;
use League\Fractal;

class SkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(Skill $skill)
    {
        return [
            "name" => (string)$skill->skill_name?: null 
           
        ];
    }
}
