<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BodymediaWorker extends Command {

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

	private $bm;

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
		if($steps == 0) return;
		$stored = Measurement::createMeasurement(
			$user,
			'steps',
			'bodymedia',
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
	public function getSteps($user, $days){
		$this->info('Retrieving data for '.$user->name.'.');

		$this->bm->setOAuthDetails($user->bodymediaToken, $user->bodymediaSecret);
		
		$startDate = \Carbon\Carbon::now()->subDays($days);
		$endDate = \Carbon\Carbon::now();

		// get activities
		$json = $this->bm->getSteps($startDate, $endDate);
		$json = json_decode($json, true);

		if( !array_key_exists("days",$json) ){
			$this->info('Error in retrieving steps from Bodymedia query.');
		}
		$steps = $json["days"];

		foreach($steps as $step){
			$timestamp = DateTime::createFromFormat('Ymd',$step["date"]);
			$value = $step["totalSteps"];

			$this->storeSteps($user->name, $value, $timestamp);
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
		$this->info('Bodymedia worker initialized. ');

		// validate days value
		// max of 60 days as Withing limits the # of queries
		$validator = Validator::make(
		    array('days' => $this->option('days')),
		    array('days' => 'required|integer|min:1|max:365')
		);
		if ($validator->fails())
		{
		    $this->info('Invalid amount of days specified. Valid ranges: 1 - 365');
		    return;
		}

		// initialize Withings client
		$key = Config::get('live.bodymedia-key');
		$secret = Config::get('live.bodymedia-secret');
		$this->bm = new  BodymediaPHP($key, $secret);

		// get all users that have Bodymedia accounts
		$bmUsers = User::whereRaw('bodymediaToken IS NOT NULL')->get();

		// get activities for each user
		foreach($bmUsers as $user){
			$this->getSteps($user, $this->option('days'));
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
			array('days', 'd', InputOption::VALUE_OPTIONAL, 'Amount of past days to fetch.', 7),
		);
	}

}