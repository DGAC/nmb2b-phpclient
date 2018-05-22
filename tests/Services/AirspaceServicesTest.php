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

use DSNA\NMB2BDriver\Services\AirspaceServices;
use PHPUnit\Framework\TestCase;

/**
 * Class AirspaceServicesTest
 */
class AirspaceServicesTest extends TestCase
{
    private $airspaceServices;

    private function getSoapClient() : AirspaceServices
    {
        if($this->airspaceServices == null) {
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
            $this->airspaceServices = new AirspaceServices(new \SoapClient($config['wsdl']['airspaceServices'], $options));
        }
        return $this->airspaceServices;
    }

    public function testRetrieveEAUPChain()
    {
        $chainDate = new \DateTime('2018-04-17');

        $result = $this->getSoapClient()->retrieveEAUPChain($chainDate);

        $this->assertEquals(5, $result->getAUPSequenceNumber());
        $this->assertEquals(18, intval($result->getLastSequenceNumber()));
    }

    public function testRetrieveEAUPRSAs()
    {
        $date = new \DateTime('2018-04-17');
        $designators = "LF*";
        $sequenceNumber = 5;

        $result = $this->getSoapClient()->retrieveEAUPRSAs($designators, $date, $sequenceNumber);

        $lfcba16b = $result->getAirspacesWithDesignator("LFCBA16B");
        $this->assertEquals(5, count($lfcba16b));

        $airspace = $lfcba16b[0];

        $this->assertEquals("LFCBA16B", \DSNA\NMB2BDriver\Models\EAUPRSAs::getAirspaceDesignator($airspace));

    }
}