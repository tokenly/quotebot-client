<?php

use PHPUnit_Framework_Assert as PHPUnit;
use Tokenly\QuotebotClient\Exceptions\ExpiredQuoteException;
use Tokenly\QuotebotClient\Mock\MockeryBuilder;

/*
* 
*/
class QuotebotTest extends PHPUnit_Framework_TestCase
{


    public function testGetRates() {
        $mockery_builder = new MockeryBuilder();
        $quotebot_client = $mockery_builder->mockQuotebotClientWithRates();

        PHPUnit::assertEquals(4000, $quotebot_client->getQuote('bitcoinAverage', ['USD','BTC'])['last']);
        PHPUnit::assertEquals(4000, $quotebot_client->getQuote('bitcoinAverage', ['USD','BTC'])['last']);
        PHPUnit::assertEquals(4000, $quotebot_client->getCurrentBTCQuoteWithFallback('bitcoinAverage', ['USD','BTC']));

        PHPUnit::assertEquals(4001, $quotebot_client->getQuote('bitstamp', ['USD','BTC'])['last']);
        PHPUnit::assertEquals(4001, $quotebot_client->getCurrentBTCQuoteWithFallback('bitstamp'));
    }

    public function testGetRatesExpired() {
        $mockery_builder = new MockeryBuilder();
        $quotebot_client = $mockery_builder->mockQuotebotClientWithRates();

        // make expired
        $quotebot_client->_setNow(strtotime('2017-09-01T00:00:00-0500'));

        // should throw exception
        $this->expectException(ExpiredQuoteException::class);
        $quotebot_client->getCurrentBTCQuoteWithFallback();
    }

    public function testGetFallback() {
        $mockery_builder = new MockeryBuilder();
        $quotebot_client = $mockery_builder->mockQuotebotClientWithRates();

        PHPUnit::assertEquals(4000, $quotebot_client->getCurrentBTCQuoteWithFallback());

        // set bitcoinAverage quote as stale
        $entries = $mockery_builder->getDefaultMockRateEntries();
        $entries[0]['time'] = '2017-08-28T00:00:00-0500';
        $mockery_builder->setMockRateEntries($entries);

        // clear cache so entries are reloaded
        $mockery_builder->getMemoryCacheStore()->clear();

        // fallback should kick in now
        PHPUnit::assertEquals(4001, $quotebot_client->getCurrentBTCQuoteWithFallback());
    }


    public function testCurrencyQuoteWithFallback() {
        $mockery_builder = new MockeryBuilder();
        $quotebot_client = $mockery_builder->mockQuotebotClientWithRates();

        // simple quote
        PHPUnit::assertEquals(0.004000, $quotebot_client->getTokenValue('poloniex', 'MYTOKEN'));


        // set bitcoinAverage quote as stale
        $entries = $mockery_builder->getDefaultMockRateEntries();
        $entries[0]['time'] = '2017-08-28T00:00:00-0500';
        $mockery_builder->setMockRateEntries($entries);

        // clear cache so entries are reloaded
        $mockery_builder->getMemoryCacheStore()->clear();

        // fallback should kick in now
        PHPUnit::assertEquals(0.004001, $quotebot_client->getTokenValue('poloniex', 'MYTOKEN'));
    }



}
