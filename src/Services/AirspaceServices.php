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

namespace DSNA\NMB2BDriver\Services;

use DSNA\NMB2BDriver\Models\EAUPChain;
use DSNA\NMB2BDriver\Models\EAUPRSAs;

/**
 * Class AirspaceServices
 * @package DSNA\NMB2BDriver\Services
 */
class AirspaceServices extends Service
{

    /**
     * @param \DateTime $chainDate
     * @return EAUPChain
     * @throws \SoapFault
     */
    public function retrieveEAUPChain(\DateTime $chainDate): EAUPChain
    {
        $now = new \DateTime('now');

        $params = array(
            'sendTime' => $now->format('Y-m-d H:i:s'),
            'chainDate' => $chainDate->format('Y-m-d')
        );

        $this->getSoapClient()->retrieveEAUPChain($params);

        return new EAUPChain($this->getSoapClient()->__getLastResponse());
    }

    /**
     * @param $designators
     * @param \DateTime $date
     * @param $sequenceNumber
     * @return EAUPRSAs
     */
    public function retrieveEAUPRSAs($designators, \DateTime $date, $sequenceNumber) : EAUPRSAs
    {
        $now = new \DateTime('now');

        $params = array(
            'sendTime' => $now->format('Y-m-d H:i:s'),
            'eaupId' => array(
                'chainDate' => $date->format('Y-m-d'),
                'sequenceNumber' => $sequenceNumber
            )
        );

        if ($designators !== null && strlen($designators) > 0) {
            $params['rsaDesignators'] = $designators;
        }

        $this->getSoapClient()->retrieveEAUPRSAs($params);

        return new EAUPRSAs($this->getSoapClient()->__getLastResponse());
    }
}