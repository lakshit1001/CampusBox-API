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
            "FollowerId" => (integer)$follow->follower_id?: 0 ,
            
       
        ];
    }

public function includeFollower(StudentFollow $follow) {
        $student = $follow->Follower;
        return $this->collection($student, new StudentTransformer);
    }
}
