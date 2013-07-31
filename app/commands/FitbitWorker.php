<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FitbitWorker extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'FitbitWorker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates data from Fitbit service.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		//
		$key = Config::get('live.fitbit-key');
		$secret = Config::get('live.fitbit-secret');

		$fitbit = new FitBitPHP($key, $key);
		$fitbit->setOAuthDetails($key, $secret);

		

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('example', InputArgument::OPTIONAL, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}