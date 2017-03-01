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

use App\SocialAccount;
use League\Fractal;

class SocialTransformer extends Fractal\TransformerAbstract
{

    public function transform(SocialAccount $social)
    {
        return [
            "id" => (integer)$social->student_id?: 0 ,
            "type" => (string)$social->type?: null ,
            "icon" => (string)$social->icon?: null ,
            "link" => (string)$social->link?: null ,
           
        ];
    }
}
