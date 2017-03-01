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

class StudentMiniTransformer extends Fractal\TransformerAbstract
{
  protected $availableIncludes = [
          'SocialAccounts',
          'Skills'
    ];
    protected $defaultIncludes = [
           'SocialAccounts',
          'Skills'
      ];
    public function transform(Student $student)
    {
        return [
            "id" => (integer)$student->student_id?: 0 ,
            "name" => (string)$student->name?: null,
            "photo" => (string)$student->image?: null,
            
            "college" => [
                "name" => (string)$student->College['name']?: null,
            ],
           
        ];
    }
  public function includeSkills(Student $student) {
        $skills = $student->Skills;

        return $this->collection($skills, new SkillTransformer);
    }
public function includeSocialAccounts(Student $student) {
        $socials = $student->SocialAccounts;

        return $this->collection($socials, new SocialTransformer);
    }
}
