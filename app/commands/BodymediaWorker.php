<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WithingsWorker extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'BodymediaWorker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates data from Bodymedia service.';

	private $withings;

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

	/*
	*	Gets user activities
	*
	*	@param {User object} $user - User to get data for.
	*	@param {Integer} - Amount of past days to fetch.
	*/
	public function getActivities($user, $days){
		$this->info('Retrieving data for '.$user->name.'.');

		$this->withings->setUser($user->withingsId);
		$this->withings->setOAuthDetails($user->withingsToken, $user->withingsSecret);
		
		for($i=0; $i < $days; $i++){
			// fetch data for the past $i days

			// calculate day
			// setTime(0,0) is used so that each timestamp does not differ in time
			// which creates duplicates
			$date = \Carbon\Carbon::now()->subDays($i)->setTime(0,0);

			// get activities
			$response = $this->withings->getActivities($date->toDateString());

			echo $date->toDateString();

			$activities = json_decode($response, true);

			if($activities['status'] == 0) {
				/*
				*	Success
				*/

				// save steps 
				if(isset($activities['body']['steps'])){
					$this->storeSteps($user->name, $activities['body']['steps'], $date);
				}
			} else if( $activities['status'] == 601){
				/*
				*	Too many requests
				*/

				$this->info('Withings API request cap reached. Cooling off for ~1 minute...');

				sleep(80); // cool off
				$i--; // continue from where we left
			} else {

			}
		}
		$this->info('OK.');
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
		$this->info('Withings worker initialized. ');

		// validate days value
		// max of 60 days as Withing limits the # of queries
		$validator = Validator::make(
		    array('days' => $this->option('days')),
		    array('days' => 'required|integer|min:1|max:59')
		);
		if ($validator->fails())
		{
		    $this->info('Invalid amount of days specified. Valid ranges: 1 - 59');
		    return;
		}

		// initialize Withings client
		$key = Config::get('live.withings-key');
		$secret = Config::get('live.withings-secret');
		$this->withings = new  WithingsPHP($key, $secret);

		// get all users that have Withings accounts
		$withingsUsers = User::whereRaw('withingsId IS NOT NULL')->get();

		// get activities for each user
		foreach($withingsUsers as $user){
			$this->getActivities($user, $this->option('days'));
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
			array('days', 'd', InputOption::VALUE_OPTIONAL, 'Amount of past days to fetch.', 1),
		);
	}

}