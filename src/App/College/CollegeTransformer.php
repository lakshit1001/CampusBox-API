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
           
        ];
    }
}
