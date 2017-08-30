<?php

namespace Tokenly\QuotebotClient\Mock;

use Exception;
use Mockery;
use Tokenly\QuotebotClient\Client;
use Tokenly\QuotebotClient\Mock\MemoryCacheStore;

/**
* Quotebot Mockery Builder
*/
class MockeryBuilder
{

    protected $rate_entries;
    protected $memory_cache_store;


    function __construct() {
        $this->initMockRateEntries();
    }
    

    ////////////////////////////////////////////////////////////////////////

    public function initMockRateEntries() {
        $this->rate_entries = $this->getDefaultMockRateEntries();
    }

    public function setMockRateEntries($rate_entries) {
        $this->rate_entries = $rate_entries;
    }

    public function mockQuotebotClientWithRates() {
        $quotebot_client_mock = Mockery::mock(Client::class, ['http://127.0.0.1/', 'fakeapitoken', $this->getMemoryCacheStore()])->makePartial()->shouldAllowMockingProtectedMethods();
        $quotebot_client_mock->shouldReceive('getQuoteDataFromAPI')->andReturnUsing(function() {
            return ['quotes' => $this->rate_entries];
        });

        // set fresh (1 min old) by default
        $quotebot_client_mock->_setNow(strtotime('2017-08-30T00:01:00-0500'));

        return $quotebot_client_mock;
    }

    public function getMemoryCacheStore() {
        if (!isset($this->memory_cache_store)) {
            $this->memory_cache_store = new MemoryCacheStore();
        }
        return $this->memory_cache_store;
    }

    public function getDefaultMockRateEntries() {
        return [
            [
                'source'     => 'bitcoinAverage',
                'pair'       => 'USD:BTC',
                'inSatoshis' => false,
                'bid'        => 3999.95,
                'last'       => 4000.00,
                'ask'        => 4000.05,
                'bidLow'     => 3999.00,
                'bidHigh'    => 4005.00,
                'bidAvg'     => 4000.00,
                'lastLow'    => 3999.00,
                'lastHigh'   => 4005.00,
                'lastAvg'    => 4000.00,
                'askLow'     => 3999.00,
                'askHigh'    => 4005.00,
                'askAvg'     => 4000.00,
                'start'      => '2017-08-30T00:00:00-0500',
                'end'        => '2017-08-30T00:00:00-0500',
                'time'       => '2017-08-30T00:00:00-0500',
            ],
            [
                'source'     => 'bitstamp',
                'pair'       => 'USD:BTC',
                'inSatoshis' => false,
                'bid'        => 3999.95,
                'last'       => 4001.00,
                'ask'        => 4001.05,
                'bidLow'     => 3999.00,
                'bidHigh'    => 4005.00,
                'bidAvg'     => 4001.00,
                'lastLow'    => 3999.00,
                'lastHigh'   => 4005.00,
                'lastAvg'    => 4001.00,
                'askLow'     => 3999.00,
                'askHigh'    => 4005.00,
                'askAvg'     => 4001.00,
                'start'      => '2017-08-30T00:00:00-0500',
                'end'        => '2017-08-30T00:00:00-0500',
                'time'       => '2017-08-30T00:00:00-0500',
            ],
            [
                'source'     => 'poloniex',
                'pair'       => 'BTC:MYTOKEN',
                'inSatoshis' => true,
                'bid'        => 95,
                'last'       => 100,
                'ask'        => 105,
                'bidLow'     => 95,
                'bidHigh'    => 105,
                'bidAvg'     => 100,
                'lastLow'    => 95,
                'lastHigh'   => 105,
                'lastAvg'    => 100,
                'askLow'     => 105,
                'askHigh'    => 105,
                'askAvg'     => 105,
                'start'      => '2017-08-30T00:00:00-0500',
                'end'        => '2017-08-30T00:00:00-0500',
                'time'       => '2017-08-30T00:00:00-0500',
            ],
        ];
    }

}