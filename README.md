# PHP Client for NM B2B Services #

[![pipeline status](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/badges/master/pipeline.svg)](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/commits/master) [![coverage report](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/badges/master/coverage.svg)](https://gitlab.asap.dsna.fr/asap/nmb2b-phpclient/commits/master)

## NM B2B Web Services

http://www.eurocontrol.int/service/network-manager-business-business-b2b-web-services

Supported versions : 21.0 - 21.5 - 22.0

## Requirements

* PHP >= 7.1

## Installation

### Composer

To install run `composer require dgac/nmb2b-phpclient`

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

//Get last AUP sequence number published before 0600 UTC
$seq = $eaupchain->getAUPSequenceNumber()
```

### Example 2

Retrieve regulations for a specified TV

```php

$start = new \DateTime('2018-04-18 00:00:00');
$end = new \DateTime('2018-04-18 23:59:59');

$regulations = $client->flowServices()->queryRegulations($start, $end, 'LF*');
```