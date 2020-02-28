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
    private $initialConstraints;

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
     * Returns a string with :
     * the rate if only one initial constraints exists
     * "rate → hour ← rate → hour ← rate" if there are several initial constraints
     * @return string
     */
    public function getNormalRates()
    {
        if(count($this->getInitialConstraints()) == 1) {
            return (string) $this->xml->initialConstraints->normalRate;
        } else {
            $str = "";
            foreach ($this->getInitialConstraints() as $constraint) {
                if(strlen($str) == 0) {
                    $str .= $constraint->getNormalRate();
                } else {
                    $str .= " → ";
                    $str .= $constraint->getDateTimeStart()->format("G:i");
                    $str .= " ← ";
                    $str .= $constraint->getNormalRate();
                }
            }
            return $str;
        }
    }

    /**
     * @return InitialConstraint[]
     */
    public function getInitialConstraints() : array
    {
        if($this->initialConstraints == null) {
            $this->initialConstraints = array();
            foreach ($this->xml->initialConstraints as $constraint) {
                $this->initialConstraints[] = new InitialConstraint($constraint);
            }
        }
        return $this->initialConstraints;
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