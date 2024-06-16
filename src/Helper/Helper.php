<?php

namespace Weirdo\Helper;

use Weirdo\Helper\Traits\HelperInt;
use Weirdo\Helper\Traits\HelperArray;
use Weirdo\Helper\Traits\HelperCarbon;
use Weirdo\Helper\Traits\HelperString;
use Weirdo\Helper\Traits\HelperCollection;

// use Weirdo\Helper\Function\Helper as FunctionHelper;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait Helper
{
    use HelperArray,
        HelperString,
        HelperCarbon,
        HelperCollection,
        HelperInt;
}
