<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use App\Skill;
use League\Fractal;

class SkillTransformer extends Fractal\TransformerAbstract
{

    public function transform(Skill $skill)
    {
        return [
            "id" => (integer)$skill->id?: 0 ,
            "name" => (string)$skill->name?: null ,
            "links"        => [
                "self" => "/reports/{$skill->id}"
            ]
        ];
    }
}
