# PHP Client for NM B2B Services #

[![pipeline status](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/badges/master/pipeline.svg)](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/commits/master) [![coverage report](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/badges/master/coverage.svg)](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/commits/master)

## NM B2B Web Services

http://www.eurocontrol.int/service/network-manager-business-business-b2b-web-services

## Requirements

* PHP >= 7.0

## Installation

### Composer

To install run `composer require dsna/nmb2b-phpclient`

## Configuration

```php
use DSNA\NMB2BDriver\NMB2BClient;

//only add path to services you intend to use
$client = new NMB2BClient(
    "path to certificate", 
    "passphrase", 
    array(
        "airspaceservices" => "path to airspace services wsdl file",
        "flowservices" => "path to flow services wsdl file"
    )
);

```

## Usage

### Example 1

Retrieve EAUP Chain from Airspace Services

```php
$eaupchain = $client->airspaceServices()->retrieveEAUPChain(new \DateTime('now'));
```
