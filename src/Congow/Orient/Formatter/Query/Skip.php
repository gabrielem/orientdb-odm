<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Skip
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Skip extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        if ($values && is_numeric($records = $values[0]) && $records >= 0) {
            return "SKIP $records";
        }
    }
}
