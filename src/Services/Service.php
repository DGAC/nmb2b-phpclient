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

use DSNA\NMB2BDriver\Exception\WSDLFileUnavailable;

class Service {

    private $client;

    private $version;

    private $versionFloat;

    private $verbose = false;

    public function __construct($wsdl, $options, $verbose)
    {
        $this->client = new \SoapClient($wsdl, $options);
        $this->extractNMVersion($wsdl);
        $this->verbose = $verbose;
    }

    public function getFullErrorMessage() {
        $text = "Last Request Header\n";
        $text .= $this->getSoapClient()->__getLastRequestHeaders()."\n\n";
        $text .= "Last Request\n";
        $text .= $this->getSoapClient()->__getLastRequest()."\n\n";
        $text .= "Last Response Header\n";
        $text .= $this->getSoapClient()->__getLastResponseHeaders()."\n\n";
        $text .= "Last Response\n";
        $text .= $this->getSoapClient()->__getLastResponse()."\n";
        return $text;
    }

    public function getSoapClient() : \SoapClient
    {
        return $this->client;
    }

    private function extractNMVersion($wsdl)
    {
        $data = file_get_contents($wsdl);
        if($data == false) {
            throw new WSDLFileUnavailable("Unable to load WSDL");
        }
        $xml = new \DOMDocument();
        $xml->loadXML($data);

        $location = $xml->getElementsByTagNameNS("http://schemas.xmlsoap.org/wsdl/soap/", "address");
        foreach ($location as $l){
            $loc = $l->getAttribute("location");
            $url = explode('/', $loc);
            $this->version = end($url);
            $aVersion = explode(".", $this->version);
            $this->versionFloat = (int) $aVersion[0] + ((int) $aVersion[1])*0.1 + ((int) $aVersion[2])*0.01;
        }
    }

    /**
     * x.y.z version of NM Services
     * @return string
     */
    public function getNMVersion() : string
    {
        return $this->version;
    }

    public function getNMVersionFloat() : float {
        return $this->versionFloat;
    }

    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    public function isVerbose()
    {
        return $this->verbose;
    }

}