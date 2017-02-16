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

use App\College;
use League\Fractal;

class CollegeTransformer extends Fractal\TransformerAbstract
{

    public function transform(College $college)
    {
        return [
            "id" => (integer)$college->id?: 0 ,
            "name" => (string)$college->name?: null ,
            "lat" => (float)$college->lat?: 0 ,
            "long" => (float)$college->long?: 0 ,
            "address" => (string)$college->address?: null ,
            "city" => (string)$college->city?: null ,
            "logo" => (string)$college->logo?: null ,
            "cover_pic" => (string)$college->cover_pic?: null ,
            "admins" => $college->CollegeAdmin[0],
            "programs" => $college->Programs[0],
            "links"        => [
                "self" => "/reports/{$college->id}"
            ]
        ];
    }
}
