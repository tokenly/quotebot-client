<?php

namespace Tokenly\QuotebotClient\Console;


use App\ConsulClient;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tokenly\Laravel\Facade\EventLog;

class GetQuoteCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quotebot:get-quote';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quotebot Quote Loader';


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['source', InputArgument::OPTIONAL, 'Quote source', 'bitcoinAverage'],
            ['pair',   InputArgument::OPTIONAL, 'Quote pair',   'USD:BTC'],
        ];
    }



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->comment('begin');

        $quotebot_client = app('Tokenly\QuotebotClient\Client');

        $source = $this->input->getArgument('source');
        $pair = explode(':', $this->input->getArgument('pair'));
        $quote = $quotebot_client->getQuote($source, $pair);
        $this->info(json_encode($quote, 192));

        $this->comment('done');
    }



}
