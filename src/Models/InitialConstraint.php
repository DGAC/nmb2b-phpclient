<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

namespace DSNA\NMB2BDriver\Models;

/**
 * Class InitialConstraint
 * @package DSNA\NMB2BDriver\Models
 */
class InitialConstraint
{

    private $xml;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function getDateTimeStart() : \DateTime
    {
        return new \DateTime($this->xml->constraintPeriod->wef . '+00:00');
    }

    public function getDateTimeEnd() : \DateTime
    {
        return new \DateTime($this->xml->constraintPeriod->unt . '+00:00');
    }

    public function getNormalRate()
    {
        return (string) $this->xml->normalRate;
    }
}