<?php

 

namespace App;

use App\StudentFollow;
use League\Fractal;

class StudentFollowTransformer extends Fractal\TransformerAbstract
{
  protected $availableIncludes = [
          'Follower'
    ];
    protected $defaultIncludes = [
           // 'Follower'
      ];
    public function transform(StudentFollow $follow)
    {
        return [
            "follower_username" => (string)$follow->follower_username?: null,
            
       
        ];
    }

public function includeFollower(StudentFollow $follow) {
        $student = $follow->Follower;
        return $this->collection($student, new StudentTransformer);
    }
}
