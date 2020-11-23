<?php
namespace App\Component;

class ORMDate extends \DateTime
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }
}