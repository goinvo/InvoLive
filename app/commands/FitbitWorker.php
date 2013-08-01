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

	// fitbit API object
	private $fitbit;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/*
	*	Gets and stores steps data
	*
	*	@param {User object} $user - 
	*/
	public function getUserSteps($user){
		$this->info('Retrieving data for '.$user->name.'.');

		// set user
		$this->fitbit->setUser($user->fitbitId);
		// get steps
		$response = $this->fitbit->getTimeSeries('steps', 'today', 'max');
		
		foreach($response as $steps){
			$date = DateTime::createFromFormat('Y-m-d', $steps->dateTime)->setTime(0,0);
			$this->storeSteps($user->name, $steps->value, $date);
		}
		$this->info('OK.');
	}

	/*
	*	Stores steps data
	*/
	public function storeSteps($user, $steps, $timestamp){
		$stored = Measurement::createMeasurement(
			$user,
			'steps',
			'fitbit',
			$steps,
			$timestamp
		);
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{

		$timestamp = \Carbon\Carbon::now()->toW3CString();
		$this->info(' ');
		$this->info($timestamp);
		$this->info('Fitbit worker initialized. ');

		// prepare fitbit client
		$key = Config::get('live.fitbit-key');
		$secret = Config::get('live.fitbit-secret');

		$this->fitbit = new FitBitPHP($key, $secret, 0, null, 'json');

		// fitbitworker is a fitbit account that will log on
		// the fitbit service and ask info about each user in the system

		// Note. In order to successfully pull data from fitbit you should either
		// A. be friends with fitbitworker (ivan@goinvo.com for now) fitbit.com
		// B. allow everyone to access you activities

		$fitbitWorker = User::find(User::getId('liveworker'));
		$this->fitbit->setOAuthDetails($fitbitWorker->fitbitToken, $fitbitWorker->fitbitSecret);

		$fitbitUsers = User::whereRaw('fitbitId IS NOT NULL')->get();

		foreach($fitbitUsers as $fitbitUser){
			$this->getUserSteps($fitbitUser);
		}

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