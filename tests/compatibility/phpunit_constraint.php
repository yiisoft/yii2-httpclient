<?php

namespace PHPUnit\Framework\Constraint {

    if (!class_exists('PHPUnit\Framework\Constraint\Constraint') && class_exists('PHPUnit_Framework_Constraint')) {
        abstract class Constraint extends \PHPUnit_Framework_Constraint
        {
        }
    }
}
