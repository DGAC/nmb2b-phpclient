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


class Airspace
{

    private $xml;

    /**
     * Airspace constructor.
     * @param \SimpleXMLElement $airspace
     */
    public function __construct(\SimpleXMLElement $airspace)
    {
        if($airspace->getName() === 'Airspace') {
            $this->xml = $airspace;
        } else {
            throw new \UnexpectedValueException('Airspace Element expected');
        }
    }

    /**
     * Return ICAO Designator of an Airspace element
     *
     * @return type
     */
    public function getDesignator() : string
    {
        $timeslices = $this->xml->children('http://www.aixm.aero/schema/5.1')->timeSlice;
        foreach ($timeslices as $timeslice) {
            $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
            foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                if ($child->getName() === 'designator') {
                    return (string) $child;
                }
            }
        }
        return "";
    }

    /**
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getTimeBegin()
    {
        $timeslices = $this->xml->children('http://www.aixm.aero/schema/5.1')->timeSlice;
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
    }

    /**
     *
     * @return \DateTime
     */
    public function getDateTimeBegin()
    {
        $timeBegin = $this->getTimeBegin();
        return new \DateTime($timeBegin . "+00:00");
    }

    /**
     * @throws \UnexpectedValueException
     * @return string
     */
    public function getTimeEnd()
    {
        $timeslices = $this->xml->children('http://www.aixm.aero/schema/5.1')->timeSlice;
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
    }

    /**
     *
     * @return \DateTime
     */
    public function getDateTimeEnd()
    {
        $timeEnd = $this->getTimeEnd();
        return new \DateTime($timeEnd . '+00:00');
    }

    /**
     *
     * @return String
     */
    public function getUpperLimit() : string
    {
        $timeslices = $this->xml->children('http://www.aixm.aero/schema/5.1')->timeSlice;
        if (count($timeslices) === 2) {
            foreach ($timeslices as $timeslice) {
                $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
                foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                    if ($child->getName() === 'activation') {
                        return (string) $child
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
    }

    /**
     * @throws \UnexpectedValueException
     * @return String
     */
    public function getLowerLimit() : string
    {
        $timeslices = $this->xml->children('http://www.aixm.aero/schema/5.1')->timeSlice;
        if (count($timeslices) === 2) {
            foreach ($timeslices as $timeslice) {
                $airspacetimeslice = $timeslice->children('http://www.aixm.aero/schema/5.1')->AirspaceTimeSlice;
                foreach ($airspacetimeslice->children('http://www.aixm.aero/schema/5.1') as $child) {
                    if ($child->getName() === 'activation') {
                        return (string) $child
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
        } else {
            throw new \UnexpectedValueException("Not a valid Airspace.");
        }
    }

}