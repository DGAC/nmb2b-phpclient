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

/**
 * Class AirspaceServices
 * @package DSNA\NMB2BDriver\Services
 */
class AirspaceServices
{

    private $soapClient;

    public function __construct($soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient() : \SoapClient
    {
        return $this->soapClient;
    }

    /**
     * @param \DateTime $chainDate
     * @return EAUPChain
     * @throws \SoapFault
     */
    public function retrieveEAUPChain(\DateTime $chainDate)
    {
        $now = new \DateTime('now');

        $params = array(
            'sendTime' => $now->format('Y-m-d H:i:s'),
            'chainDate' => $chainDate->format('Y-m-d')
        );

        $this->soapClient->retrieveEAUPChain($params);

        return new EAUPChain($this->soapClient->__getLastResponse());
    }
}