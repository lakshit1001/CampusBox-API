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

use App\Student;
use League\Fractal;

class StudentTransformer extends Fractal\TransformerAbstract
{

    public function transform(Student $student)
    {
        return [
            "id" => (integer)$student->student_id?: 0 ,
            "college_id" => (integer)$student->college_id?: null,
            "name" => (string)$student->name?: null,
            "username" => (string)$student->username?: null,
            "roll_number" => (integer)$student->roll_number?: null,
            "email" => (string)$student->email?: null,
            "phone" => (integer)$student->phone?: null,
            "photo" => (string)$student->photo?: null,
            "hostelid" => (integer)$student->hostelid?: null,
            "room_number" => (string)$student->room_number?: null,
            "home_city" => (string)$student->home_city?: null,
            "grad_id" => (integer)$student->grad_id?: null,
            "branch_id" => (integer)$student->branch_id?: null,
            "year" => (string)$student->year?: null,
            "class_id" => (integer)$student->class_id?: null,
            "passout_year" => (integer)$student->passout_year?: null,
            "age" => (integer)$student->age?: null,
            "gender" => (string)$student->gender?: null,
            "college" => (string)$student->College['name']?: null,
            "skills" => $student->Skills[0],
            "skill_name" => (string)$student->SkillList[$student->Skills[0]->id]?: null,

            "links"        => [
                "facebook" => (string)$student->Socialid['facebook']?: null,
                "instagram" => (string)$student->Socialid['instagram']?: null,
                "github" => (string)$student->Socialid['github']?: null,
                "behance" => (string)$student->Socialid['behance']?: null,
                "soundcloud" => (string)$student->Socialid['soundcloud']?: null,
                "linkedin" => (string)$student->Socialid['linkedin']?: null,
                "other" => (string)$student->Socialid['other']?: null,
                "self" => "/students/{$student->id}"
            ]
        ];
    }
}
