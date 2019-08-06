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
    private $version;

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
            $this->version = $config['version'];
            $this->flowServices = new FlowServices($config['wsdl']['flowServices'], $options);
        }
        return $this->flowServices;
    }

    /**
     * @covers \DSNA\NMB2BDriver\Services\Service::getNMVersion()
     * @covers \DSNA\NMB2BDriver\Services\Service::extractNMVersion()
     */
    public function testGetNMVersion()
    {
        $nmVersion = $this->getSoapClient()->getNMVersion();
        $this->assertEquals($this->version, $nmVersion);
    }

    /**
     * @return \DSNA\NMB2BDriver\Models\Regulation
     */
    public function testQueryRegulations()
    {
        $start = new \DateTime('2019-08-01 ');
        $start->setTime(0,0);
        $end = new \DateTime('2019-08-01 ');
        $end->setTime(23,59);

        $result = $this->getSoapClient()->queryRegulations($start, $end, "EG*");

        $this->assertEquals(4, count($result->getRegulationsAsXML()));
        $this->assertEquals(4, count($result->getRegulations()));

        $result = $this->getSoapClient()->queryRegulations($start, $end);

        $this->assertEquals(66, count($result->getRegulationsAsXML()));
        $this->assertEquals(66, count($result->getRegulations()));

        $this->assertInstanceOf(\DSNA\NMB2BDriver\Models\Regulation::class, $result->getRegulations()[0]);

        $regul = $result->getRegulationsAsXML()[0];

        return new \DSNA\NMB2BDriver\Models\Regulation($regul);

    }

    /**
     * @param \DSNA\NMB2BDriver\Models\Regulation $regulation
     * @depends testQueryRegulations
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getDataId()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getRegulationName()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getDescription()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getNormalRate()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getReason()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getDateTimeStart()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getDateTimeEnd()
     * @covers \DSNA\NMB2BDriver\Models\Regulation::getRegulationState()
     */
    public function testRegulation(\DSNA\NMB2BDriver\Models\Regulation $regulation)
    {

        $this->assertEquals("ME301", $regulation->getDataId());
        $this->assertEquals("LFME3", $regulation->getRegulationName());
        $this->assertEquals("LFMM -  E3 SECTOR", $regulation->getDescription());
        $this->assertEquals("34", $regulation->getNormalRate());
        $this->assertEquals("ATC_CAPACITY", $regulation->getReason());

        $wef = "2019-08-01 10:15" . '+00:00';
        $dateWef = new DateTime($wef);
        $unt = "2019-08-01 13:00" . '+00:00';
        $dateUnt = new DateTime($unt);

        $this->assertEquals($dateWef, $regulation->getDateTimeStart());
        $this->assertEquals($dateUnt, $regulation->getDateTimeEnd());

        $this->assertEquals("TERMINATED", $regulation->getRegulationState());
    }
}