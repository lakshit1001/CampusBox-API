<?php

 

namespace App;

use App\Todo;
use League\Fractal;

class TodoTransformer extends Fractal\TransformerAbstract
{

    public function transform(Todo $todo)
    {
        return [
            "uid" => (string)$todo->uid ?: null,
            "order" => (integer)$todo->order ?: 40,
            "title" => (string)$todo->title ?: null,
            "completed" => !!$todo->completed,
            "links"        => [
                "self" => "/todos/{$todo->uid}"
            ]
        ];
    }
}
