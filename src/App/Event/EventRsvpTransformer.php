<?php

 

namespace App;

use App\EventRsvp;
use League\Fractal;

class EventRsvpTransformer extends Fractal\TransformerAbstract
{
    public function transform(EventRsvp $x)
    {
        return [
            "Username" => (string)$x->username?: 0 ,
        ];
    }
}
