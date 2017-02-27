<?php

namespace App;

class Token
{
    public $decoded;

    public function hydrate($decoded)
    {
        $this->decoded = $decoded;
    }

    public function hasScope(array $scope)
    {
        return true;
        //return !!count(array_intersect($scope, (array)$this->decoded->scope));
    }
}
