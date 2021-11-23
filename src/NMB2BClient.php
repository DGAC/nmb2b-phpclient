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

namespace DSNA\NMB2BDriver;

use DSNA\NMB2BDriver\Exception\UnsupportedNMVersion;
use DSNA\NMB2BDriver\Exception\WSDLFileUnavailable;
use DSNA\NMB2BDriver\Services\AirspaceServices;
use DSNA\NMB2BDriver\Services\FlowServices;

/**
 * Class NMB2BClientFactory
 * @package DSNA\NMB2BDriver
 */
class NMB2BClient
{

    private $verbose = false;

    private $airspaceServices;
    private $flowServices;

    public const SUPPORTED_VERSIONS = ["23.0.0", "23.5.0", "24.0.0", "25.0.0"];

    /**
     * Default options
     * @var array
     */
    private $options = array(
        'trace' => 1,
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_NONE
    );

    private $wsdl;

    /**
     * NMB2BClient constructor.
     * @param $certPath
     * @param $passphrase
     * @param array $wsdl
     * @param $options
     */
    public function __construct($certPath, $passphrase, array $wsdl, $options)
    {
        $this->options['stream_context'] = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        ));
        $this->options = array_merge($this->options, $options);
        $this->options['local_cert'] = $certPath;
        $this->options['passphrase'] = $passphrase;

        $this->wsdl = $wsdl;
    }

    /**
     * @return AirspaceServices
     * @throws WSDLFileUnavailable
     * @throws UnsupportedNMVersion
     */
    public function airspaceServices() : AirspaceServices
    {
        if($this->airspaceServices == null)
        {
            if(array_key_exists('airspaceservices', $this->wsdl)) {
                if(file_exists($this->wsdl['airspaceservices'])) {
                    $this->airspaceServices = new AirspaceServices($this->wsdl['airspaceservices'], $this->options, $this->verbose);
                    if(!in_array($this->airspaceServices->getNMVersion(), NMB2BClient::SUPPORTED_VERSIONS)) {
                        throw new UnsupportedNMVersion($this->airspaceServices->getNMVersion() . ' is not supported.');
                    }
                } else {
                    throw new WSDLFileUnavailable('AirspaceServices WSDL is not a file.');
                }
            } else {
                throw new WSDLFileUnavailable('Path to AirspaceServices WSDL file missing.');
            }
        }
        return $this->airspaceServices;
    }

    public function flowServices() : FlowServices
    {
        if($this->flowServices == null)
        {
            if(array_key_exists('flowservices', $this->wsdl)) {
                if(file_exists($this->wsdl['flowservices'])) {
                    $this->flowServices = new FlowServices($this->wsdl['flowservices'], $this->options, $this->verbose);
                    if(!in_array($this->flowServices->getNMVersion(), NMB2BClient::SUPPORTED_VERSIONS)) {
                        throw new UnsupportedNMVersion($this->flowServices->getNMVersion() . ' is not supported.');
                    }
                } else {
                    throw new WSDLFileUnavailable('FlowServices WSDL is not a file.');
                }
            } else {
                throw new WSDLFileUnavailable('Path to FlowServices WSDL file missing.');
            }
        }
        return $this->flowServices;
    }

    public function setVerbose(bool $verbose)
    {
        $this->verbose = $verbose;
        if($this->airspaceServices) {
            $this->airspaceServices->setVerbose($verbose);
        }
        if($this->flowServices) {
            $this->flowServices->setVerbose($verbose);
        }
    }

}
