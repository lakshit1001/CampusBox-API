<?php

 

namespace App;

use App\Student;
use League\Fractal;

class StudentMiniTransformer extends Fractal\TransformerAbstract
{
  protected $availableIncludes = [
          // 'SocialAccounts',
          'Skills'
    ];
    protected $defaultIncludes = [
           // 'SocialAccounts',
          'Skills'
      ];
    public function transform(Student $student)
    {
        return [
            "username" => (string)$student->username?: 0 ,
            "title" => (string)$student->name?: null,
            "photo" => (string)$student->image?: null,
            "about" => (string)$student->about?: null,
            "college" => [
                "name" => (string)$student->College['name']?: null,
            ],
           
        ];
    }
  public function includeSkills(Student $student) {
        $skills = $student->Skills;

        return $this->collection($skills, new StudentSkillTransformer);
    }
// public function includeSocialAccounts(Student $student) {
//         $socials = $student->SocialAccounts;

//         return $this->collection($socials, new SocialTransformer);
//     }
}
