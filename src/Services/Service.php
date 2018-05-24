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

class Service {

    private $soapClient;

    public function __construct(\SoapClient $client)
    {
        $this->soapClient = $client;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient() : \SoapClient
    {
        return $this->soapClient;
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

    /**
     * x.y.z version of NM Services
     * @return string
     */
    public function getNMVersion()
    {
        $url = explode("/",$this->getSoapClient()->__setLocation());
        return end($url);
    }

}