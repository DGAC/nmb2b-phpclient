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

class EAUPRSAs extends SoapResponse
{
    public function __construct($strXML)
    {
        parent::__construct($strXML);
        $this->getXML()->registerXPathNamespace('adrmsg', "http://www.eurocontrol.int/cfmu/b2b/ADRMessage");
        $this->getXML()->registerXPathNamespace('gml', "http://www.opengis.net/gml/3.2");
        $this->getXML()->registerXPathNamespace('aixm', 'http://www.aixm.aero/schema/5.1');
    }


    /**
     * Get all Airspace elements whith a designator starting with <code>$designator</code>
     *
     * @param type $designator
     * @return array
     */
    public function getAirspacesWithDesignator($designator)
    {
        if($this->getXML() != null) {
            return $this->getXML()->xpath('//aixm:Airspace//aixm:AirspaceTimeSlice[starts-with(aixm:designator,"' . $designator . '")]/../..');
        } else {
            return null;
        }
    }

    /**
     * Return ICAO Designator of an Airspace element
     *
     * @param \SimpleXMLElement $airspace
     * @return type
     * @throws \UnexpectedValueException
     */
    public static function getAirspaceDesignator(\SimpleXMLElement $airspace)
    {
        if ($airspace->getName() === 'Airspace') {
            $timeslices = $airspace->children('http://www.aixm.aero/schema/5.1')->timeSlice;
            foreach ($timeslices as $timeslice) {
                $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
                foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                    if ($child->getName() === 'designator') {
                        return $child;
                    }
                }
            }
            return "";
        } else {
            throw new \UnexpectedValueException("Airspace Element expected.");
        }
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return string
     * @throws \UnexpectedValueException
     */
    public static function getAirspaceTimeBegin(\SimpleXMLElement $airspace)
    {
        if ($airspace->getName() === 'Airspace') {
            $timeslices = $airspace->children('http://www.aixm.aero/schema/5.1')->timeSlice;
            if (count($timeslices) >= 2) {
                foreach ($timeslices as $timeslice) {
                    $validtime = $timeslice
                        ->children('http://www.aixm.aero/schema/5.1')
                        ->AirspaceTimeSlice
                        ->children('http://www.opengis.net/gml/3.2')
                        ->validTime;
                    foreach ($validtime->children('http://www.opengis.net/gml/3.2') as $child) {
                        if ($child->getName() === 'TimePeriod') {
                            return $child->children('http://www.opengis.net/gml/3.2')->beginPosition;
                        }
                    }
                }
            } else {
                throw new \UnexpectedValueException("Not a valid Airspace.");
            }
        } else {
            throw new \UnexpectedValueException("Airspace Element expected.");
        }
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return \DateTime
     */
    public static function getAirspaceDateTimeBegin(\SimpleXMLElement $airspace)
    {
        $timeBegin = EAUPRSAs::getAirspaceTimeBegin($airspace);
        return new \DateTime($timeBegin . "+00:00");
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return string
     * @throws \UnexpectedValueException
     */
    public static function getAirspaceTimeEnd(\SimpleXMLElement $airspace)
    {
        if ($airspace->getName() === 'Airspace') {
            $timeslices = $airspace->children('http://www.aixm.aero/schema/5.1')->timeSlice;
            if (count($timeslices) === 2) {
                foreach ($timeslices as $timeslice) {
                    $validtime = $timeslice
                        ->children('http://www.aixm.aero/schema/5.1')
                        ->AirspaceTimeSlice
                        ->children('http://www.opengis.net/gml/3.2')
                        ->validTime;
                    foreach ($validtime->children('http://www.opengis.net/gml/3.2') as $child) {
                        if ($child->getName() === 'TimePeriod') {
                            return $child->children('http://www.opengis.net/gml/3.2')->endPosition;
                        }
                    }
                }
            } else {
                throw new \UnexpectedValueException("Not a valid Airspace.");
            }
        } else {
            throw new \UnexpectedValueException("Airspace Element expected.");
        }
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return \DateTime
     */
    public static function getAirspaceDateTimeEnd(\SimpleXMLElement $airspace)
    {
        $timeEnd = EAUPRSAs::getAirspaceTimeEnd($airspace);
        return new \DateTime($timeEnd . '+00:00');
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return String
     * @throws \UnexpectedValueException
     */
    public static function getAirspaceUpperLimit(\SimpleXMLElement $airspace)
    {
        if ($airspace->getName() === 'Airspace') {
            $timeslices = $airspace->children('http://www.aixm.aero/schema/5.1')->timeSlice;
            if (count($timeslices) === 2) {
                foreach ($timeslices as $timeslice) {
                    $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
                    foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                        if ($child->getName() === 'activation') {
                            return $child
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->AirspaceActivation
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->levels
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->AirspaceLayer
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->upperLimit;
                        }
                    }
                }
            }
        } else {
            throw new \UnexpectedValueException("Airspace Element expected.");
        }
    }

    /**
     *
     * @param \SimpleXMLElement $airspace
     * @return String
     * @throws \UnexpectedValueException
     */
    public static function getAirspaceLowerLimit(\SimpleXMLElement $airspace)
    {
        if ($airspace->getName() === 'Airspace') {
            $timeslices = $airspace->children('http://www.aixm.aero/schema/5.1')->timeSlice;
            if (count($timeslices) === 2) {
                foreach ($timeslices as $timeslice) {
                    $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
                    foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                        if ($child->getName() === 'activation') {
                            return $child
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->AirspaceActivation
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->levels
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->AirspaceLayer
                                ->children('http://www.aixm.aero/schema/5.1')
                                ->lowerLimit;
                        }
                    }
                }
            }
        } else {
            throw new \UnexpectedValueException("Airspace Element expected.");
        }
    }
}