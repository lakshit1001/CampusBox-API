<?php

 

namespace App;

use App\SocialAccount;
use League\Fractal;

class SocialTransformer extends Fractal\TransformerAbstract
{

    public function transform(SocialAccount $social)
    {
        return [
            "id" => (integer)$social->username?: 0 ,
            "type" => (string)$social->type?: null ,
            "icon" => (string)$social->icon?: null ,
            "link" => (string)$social->link?: null ,
           
        ];
    }
}
