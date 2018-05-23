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

}