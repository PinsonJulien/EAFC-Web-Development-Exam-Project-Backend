<?php

namespace App\Helpers\Operators\CombinedOperators;

use App\Helpers\Operators\Equal;
use App\Helpers\Operators\NotEqual;
use App\Helpers\Operators\GreaterThan;
use App\Helpers\Operators\GreaterThanOrEqualTo;
use App\Helpers\Operators\LessThan;
use App\Helpers\Operators\LessThanOrEqualTo;

/**
 * Class to represent a group of Number Operators
 */
class NumberOperators extends CombinedOperator {
    /**
     * Constructor building a CombinedOperator to group number operators.
     */
    public function __construct()
    {
        parent::__construct([
            new Equal(),
            new NotEqual(),
            new LessThan(),
            new LessThanOrEqualTo(),
            new GreaterThan(),
            new GreaterThanOrEqualTo(),
        ]);
    }
}
