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
 * Class EAUPChain
 * @package DSNA\NMB2BDriver\Models
 */
class EAUPChain extends SoapResponse
{

    public function getLastSequenceNumber()
    {
        $sequenceNumber = - 1;
        foreach ($this->getXML()
                     ->children('http://schemas.xmlsoap.org/soap/envelope/')
                     ->Body
                     ->children('eurocontrol/cfmu/b2b/AirspaceServices')
                     ->EAUPChainRetrievalReply
                     ->children('')
                     ->data
                     ->chain
                     ->eaups as $eaup) {
            $sequenceNumber = $eaup->eaupId->sequenceNumber;
        }
        return $sequenceNumber;
    }

    /**
     * Return the sequence number corresponding to the last UUP which validity->wef is 0600
     * @return int
     */
    public function getAUPSequenceNumber()
    {
        $sequenceNumber = -1;
        $validity = new \DateTime($this->getXML()
                ->children('http://schemas.xmlsoap.org/soap/envelope/')
                ->Body
                ->children('eurocontrol/cfmu/b2b/AirspaceServices')
                ->EAUPChainRetrievalReply
                ->children('')
                ->data
                ->chain
                ->chainDate . ' 06:00:00');
        foreach ($this->getXML()
                     ->children('http://schemas.xmlsoap.org/soap/envelope/')
                     ->Body
                     ->children('eurocontrol/cfmu/b2b/AirspaceServices')
                     ->EAUPChainRetrievalReply
                     ->children('')
                     ->data
                     ->chain
                     ->eaups as $eaup) {
            $wef = new \DateTime($eaup->validityPeriod->wef);
            $seq = intval($eaup->eaupId->sequenceNumber);
            if($wef == $validity && $sequenceNumber < $seq) {
                $sequenceNumber = $seq;
            }
        }
        return $sequenceNumber;
    }
}