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
 * Class SoapResponse
 * @package DSNA\NMB2BDriver\Models
 */
class SoapResponse
{
    private $xml;
    private $str;

    public function __construct($strXML)
    {

        $this->xml = new \SimpleXMLElement($strXML);
    }

    public function getXML()
    {
        return $this->xml;
    }

    public function getString()
    {
        return $this->str;
    }
}