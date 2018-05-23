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

use DSNA\NMB2BDriver\Services\FlowServices;

use DSNA\NMB2BDriver\Models\RegulationListReply;

use PHPUnit\Framework\TestCase;

/**
 * Class FlowServicesTest
 */
class FlowServicesTest extends TestCase
{
    private $flowServices;

    private function getSoapClient() : FlowServices
    {
        if($this->flowServices == null) {
            $config = include('./tests/config.php');
            $options = array(
                'trace' => 1,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE
            );
            $options['stream_context'] = stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            ));
            $options['local_cert'] = $config['certPath'];
            $options['passphrase'] = $config['passphrase'];
            $options['proxy_host'] = $config['proxyhost'];
            $options['proxy_port'] = $config['proxyport'];
            $this->flowServices = new FlowServices(new \SoapClient($config['wsdl']['flowServices'], $options));
        }
        return $this->flowServices;
    }

    public function testQueryRegulations()
    {
        $start = new \DateTime('2018-05-18 ');
        $start->setTime(0,0);
        $end = new \DateTime('2018-05-18 ');
        $end->setTime(23,59);

        $result = $this->getSoapClient()->queryRegulations($start, $end, "EG*");

        $this->assertEquals(11, count($result->getRegulations()));

        $result = $this->getSoapClient()->queryRegulations($start, $end);

        $this->assertEquals(34, count($result->getRegulations()));

        $regul = $result->getRegulations()[0];

        $this->assertEquals("LFBOA18E", RegulationListReply::getDataId($regul));
        $this->assertEquals("LFBOARR", RegulationListReply::getRegulationName($regul));
        $this->assertEquals("LFBO ARRIVALS", RegulationListReply::getDescription($regul));
        $this->assertEquals("10", RegulationListReply::getNormalRate($regul));
        $this->assertEquals("WEATHER", RegulationListReply::getReason($regul));

        $wef = "2018-05-18 05:30" . '+00:00';
        $dateWef = new DateTime($wef);
        $unt = "2018-05-18 09:00" . '+00:00';
        $dateUnt = new DateTime($unt);

        $this->assertEquals($dateWef, RegulationListReply::getDateTimeStart($regul));
        $this->assertEquals($dateUnt, RegulationListReply::getDateTimeEnd($regul));

        $this->assertEquals("CANCELLED", RegulationListReply::getRegulationState($regul));

    }
}