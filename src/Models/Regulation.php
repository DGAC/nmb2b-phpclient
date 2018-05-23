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
 * Class Regulation
 * Implements FlowServices\Regulation Chapter 4.91
 * @package DSNA\NMB2BDriver\Models
 */
class Regulation //extends RegulationOrMCDMOnly
{

    private $xml;

    public function __construct(\SimpleXMLElement $regulation)
    {
        //TODO check if element if ok
        $this->xml = $regulation;
    }

    /**
     * @return string
     */
    public function getDataId() {
        return (string) $this->xml->regulationId;
    }

    /**
     * @return string
     */
    public function getRegulationName() {
        return (string) $this->xml->location->id;
    }

    /**
     * element->location->description
     * @return string
     */
    public  function getDescription() {
        return (string) $this->xml->location->description;
    }

    /**
     * initialConstraints->normalRate
     * @return string
     */
    public function getNormalRate()
    {
        return (string) $this->xml->initialConstraints->normalRate;
    }

    /**
     * @return string
     */
    public function getReason() {
        return (string) $this->xml->reason;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeStart() {
        $time = $this->xml->applicability->wef . '+00:00';
        return new \DateTime($time);
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeEnd() {
        $time = $this->xml->applicability->unt . '+00:00';
        return new \DateTime($time);
    }

    /**
     * @return string
     */
    public function getRegulationState()
    {
        return (string) $this->xml->regulationState;
    }
}