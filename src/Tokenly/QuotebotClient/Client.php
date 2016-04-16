<?php

namespace Tokenly\QuotebotClient;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Tokenly\QuotebotClient\Contracts\CacheStore;

/**
* Quotebot Client
*/
class Client
{

    const SATOSHI = 100000000;
    
    function __construct($quotebot_url, $api_token, CacheStore $cache_store)
    {
        $this->quotebot_url = $quotebot_url;
        $this->api_token    = $api_token;
        $this->cache_store  = $cache_store;
    }

    public function getCurrencyValue($source, array $pair, $quote_type='last', $fiat_source='bitcoinAverage', array $fiat_pair=['USD','BTC'], $fiat_quote_type='last') {
        $crypto_quote = $this->getQuote($source, $pair);
        $fiat_quote = $this->getQuote($fiat_source, $fiat_pair);

        $crypto_value = $crypto_quote[$quote_type];
        $fiat_value = $fiat_quote[$fiat_quote_type];

        if ($crypto_quote['inSatoshis']) {
            $crypto_value = $crypto_value / self::SATOSHI;
        }

        return $crypto_value * $fiat_value;
    }

    public function getQuote($source, array $pair) {
        $pair_string = $pair[0].':'.$pair[1];

        $cached_value = $this->cache_store->get($source.'.'.$pair_string);
        if ($cached_value !== null AND $cached_value) {
            return $cached_value;
        }

        $quote = $this->loadQuote($source, $pair);
        return $quote;
    }

    public function loadQuote($source=null, array $pair=array()) {
        $quote = null;
        $pair_string = null;
        if ($source !== null) { $pair_string = $pair[0].':'.$pair[1]; }

        $loaded_quote_data = $this->getQuoteDataFromAPI();
        foreach($loaded_quote_data['quotes'] as $loaded_quote) {
            $loaded_source = $loaded_quote['source'];
            $loaded_pair_string = $loaded_quote['pair'];

            // cache for 10 minutes
            $this->cache_store->put($loaded_source.'.'.$loaded_pair_string, $loaded_quote, 10);

            if ($source !== null) {
                if ($loaded_source == $source AND $loaded_pair_string == $pair_string) {
                    $quote = $loaded_quote;
                }
            }
        }

        if ($quote === null) { throw new Exception("Quote not found for $source with pair $pair_string", 1); }

        return $quote;
    }


    protected function getQuoteDataFromAPI() {
        $api_path = '/api/v1/quote/all';

        $client = new GuzzleClient();

        $data = ['apitoken' => $this->api_token];
        $request = new \GuzzleHttp\Psr7\Request('GET', $this->quotebot_url.$api_path);
        $request = \GuzzleHttp\Psr7\modify_request($request, ['query' => http_build_query($data, null, '&', PHP_QUERY_RFC3986)]);

        // send request
        try {
            $response = $client->send($request);
        } catch (RequestException $e) {
            if ($response = $e->getResponse()) {
                // interpret the response and error message
                $code = $response->getStatusCode();
                try {
                    $json = json_decode($response->getBody(), true);
                } catch (Exception $parse_json_exception) {
                    // could not parse json
                    $json = null;
                }
                if ($json and isset($json['message'])) {
                    throw new Exception($json['message'], $code);
                }
            }

            // if no response, then just throw the original exception
            throw $e;
        }

        $code = $response->getStatusCode();
        if ($code == 204) {
            // empty content
            return [];
        }

        $json = json_decode($response->getBody(), true);
        if (!is_array($json)) { throw new Exception("Unexpected response", 1); }

        return $json;
    }

}
