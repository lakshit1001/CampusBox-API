<?php

 

namespace App;

use App\Student;
use League\Fractal;

class StudentMiniTransformer extends Fractal\TransformerAbstract
{
    public function transform(Student $student)
    {
        return [
            "username" => (string)$student->username?: 0 ,
            "title" => (string)$student->name?: null,
            "about" => (string)$student->about?: null,
            "photo" => (string)$student->image?: null,
            "college" => [
                "name" => (string)$student->College['name']?: null,
            ],
           
        ];
    }
}
