<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Collection class
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Types\Rid;

use Doctrine\ODM\OrientDB\Types\Rid;

class Collection extends Rid
{
    protected $rids;

    /**
     * Instatiates a new collection, setting the $rids belonging to it.
     *
     * @param Array $rids
     */
    public function __construct($rids)
    {
        $this->rids = $rids;
    }

    /**
     * Returns the rids associated to the collection.
     *
     * @return Array
     */
    public function getValue()
    {
        return $this->rids;
    }
}
