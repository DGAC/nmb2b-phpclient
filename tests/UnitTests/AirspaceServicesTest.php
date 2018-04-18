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

namespace DSNA\NMB2BDriver\Tests;

use DSNA\NMB2BDriver\Models\EAUPChain;
use PHPUnit\Framework\TestCase;

class AirspaceServicesTest extends TestCase
{
    private $soapClient;

    private function getSoapClient()
    {
        if($this->soapClient == null) {
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
            $this->soapClient = new \SoapClient($config['wsdl']['airspaceServices'], $options);
        }
        return $this->soapClient;
    }

    public function testRetrieveEAUPChain()
    {
        $now = new \DateTime('now');

        $chainDate = new \DateTime('2018-04-17');

        $params = array(
            'sendTime' => $now->format('Y-m-d H:i:s'),
            'chainDate' => $chainDate->format('Y-m-d')
        );

        $this->getSoapClient()->retrieveEAUPChain($params);

        echo "Response \n";
        echo $this->getSoapClient()->__getLastRequestHeaders();
        echo $this->getSoapClient()->__getLastRequest();
        echo $this->getSoapClient()->__getLastResponseHeaders();
        echo $this->getSoapClient()->__getLastResponse();
        try {
            $result = new EAUPChain($this->getSoapClient()->__getLastResponse());
        } catch( \Exception $e) {
            echo $e->getMessage();
            echo "Response :\n";
            echo $this->getSoapClient()->__getLastResponse();
            $this->fail();
        }
        $this->assertEquals($result->getAUPSequenceNumber(), 17);
    }
}