<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class StoryDoesExist extends Constraint
{
    //Set Parameter vom StoryDoesExistValidator muss gleich heissen wie hier die {{StoryID}}
    public string $message = "Die Story mit ID {{StoryId}} existiert nicht";
}