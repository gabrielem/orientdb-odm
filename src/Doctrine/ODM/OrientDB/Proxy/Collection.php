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
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Proxy;

use Doctrine\ODM\OrientDB\Manager;

class Collection extends AbstractProxy
{
    protected $manager;
    protected $rids;
    protected $collection;

    /**
     * Instantiates a new Proxy collection.
     *
     * @param Manager   $manager
     * @param array     $rids
     */
    function __construct(Manager $manager, Array $rids)
    {
        $this->manager = $manager;
        $this->rids = $rids;
    }

    /**
     * Returns the array of records associated with this proxy.
     *
     * @return Array
     */
    public function __invoke()
    {
        if (!$this->collection) {
            $rids = $this->getRids();
            $this->collection = $this->getManager()->findRecords($rids);
        }

        return $this->collection;
    }

    /**
     * Returns the RIDs to find.
     *
     * @return array
     */
    protected function getRids()
    {
        return $this->rids;
    }
}
