# Quotebot Client

A quotebot client library for Tokenly.

[![Build Status](https://travis-ci.org/tokenly/quotebot-client.svg?branch=master)](https://travis-ci.org/tokenly/quotebot-client)


# Installation

### Add the package via composer

```
composer require tokenly/quotebot-client
```

## Usage with Laravel

### Add the Service Provider

Add the following to the `providers` array in your application config:

```
Tokenly\QuotebotClient\ServiceProvider\QuotebotServiceProvider::class,
```

### Set the environment variables

```
QUOTEBOT_API_TOKEN=http://quotebot.tokenly.co
QUOTEBOT_API_TOKEN=my-api-token
```


### Simple BTC quote

Get a BTC in USD.  This will use bitcoinAverage and then fallback to bitstamp if the data is not current

```php
$quotebot_client = app('Tokenly\QuotebotClient\Client');
$usd_float = $quotebot_client->getCurrentBTCQuoteWithFallback();
```


### Get a token quote

Get a token quote by going to BTC and then from BTC to USD.  This will use the default fallback sources of bitcoinAverage and bitstamp for the BTC quote.

```php
$quotebot_client = app('Tokenly\QuotebotClient\Client');
$usd_float = $quotebot_client->getTokenValue('poloniex', 'XCP');
```


