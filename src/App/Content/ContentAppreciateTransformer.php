<?php

 

namespace App;

use App\Student;
use League\Fractal;

class ContentAppreciateTransformer extends Fractal\TransformerAbstract
{
    public function transform(ContentAppreciate $student)
    {
        return [
            "username" => (string)$student->username?: 0 ,
            "content_appreciate_id" => (int)$student->content_appreciate_id?: 0,
            "content_id" => (int)$student->content_id?: 0,
           
        ];
    }
}
