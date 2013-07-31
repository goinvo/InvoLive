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
	protected $name = 'WithingsWorker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates data from Withings service.';

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

	public function storeSteps($user, $steps, $timestamp){
		$stored = Measurement::createMeasurement(
			$user,
			'steps',
			'fitbit',
			$steps,
			$timestamp
		);
	}

	public function getActivities($user, $days){
		$this->withings->setUser($user->withingsId);
		$this->withings->setOAuthDetails($user->withingsToken, $user->withingsSecret);
		
		for($i=0; $i < $days; $i++){
			// fetch data for past days
			// setTime(0,0) is used so that each timestamp does not differ in time
			// which creates duplicates
			$date = \Carbon\Carbon::now()->subDays($i)->setTime(0,0);
			$response = $this->withings->getActivities($date->toDateString());
			$activities = json_decode($response, true);

			if($activities['status'] == 0) {
				//success

				// save steps 
				if(isset($activities['body']['steps'])){
					$this->storeSteps($user->name, $activities['body']['steps'], $date);
				}
			} else if( $activities['status'] == 601){
				$this->info('Withings API request cap reached. Cooling off for ~1 minute...');
				sleep(80); // cool off
				$i--; // continue from where we left
			} else {

			}
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{

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

		$key = Config::get('live.withings-key');
		$secret = Config::get('live.withings-secret');

		$this->withings = new  WithingsPHP($key, $secret);

		$withingsUsers = User::whereRaw('withingsId IS NOT NULL')->get();

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