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
           'Follower'
      ];
    public function transform(StudentFollow $follow)
    {
        return [
            "FollowerId" => (integer)$follow->follower_username?: 0 ,
            
       
        ];
    }

public function includeFollower(StudentFollow $follow) {
        $student = $follow->Follower;
        return $this->collection($student, new StudentTransformer);
    }
}
