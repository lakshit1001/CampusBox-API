<?php

 

namespace App;

use App\College;
use League\Fractal;

class CollegeTransformer extends Fractal\TransformerAbstract
{

    public function transform(College $college)
    {
        return [
            "id" => (integer)$college->college_id?: 0 ,
            "name" => (string)$college->name?: null ,
            "lat" => (float)$college->lat?: 0 ,
            "long" => (float)$college->longitude?: 0 ,
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
