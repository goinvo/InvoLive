<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StaffplanWorker extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'StaffplanWorker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates data from Staffplan service.';

	private $users;
	private $projects; 
	private $clients;
	private $validUIDs = array();

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
	*	Get Staffplan JSON
	*	Needs to be changed as we get a new API
	*/
	public function getStaffplanData(){
		$json_url = 'http://live.dev/data.json';
		$json_string = curl_init($json_url);

		$ch = curl_init( $json_url );

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
			CURLOPT_POSTFIELDS => $json_string
		);

		curl_setopt_array( $ch, $options );

		$result =  curl_exec($ch); 
		return json_decode($result, true);
	}

	/*
	*	Delete all staffplan entries
	*/
	public function deleteStaffplanEntries(){
		// need to call delete on models instead of raw query to delete related attributes
		$entries = Measurement::where('source_id', Source::getId('staffplan'))->get();
		foreach($entries as $entry){
			$entry->delete();
		}
	}

	/*
	*	Find involive users in staffplan user array
	*/
	public function filterUsers($userdata){
		$filtered = array();
		foreach($userdata as $user){
			if(User::getId($user['email']) != null) {
				array_push($filtered, $user);
				array_push($this->validUIDs, $user['id']);
			}
		}
		return $filtered;
	}

	/*
	*	Gets entry property
	*
	*	@param {integer} entry_id - id for entry from which property has to be extracted
	*	@param {array} entries - array of etnries
	*	@param {string} attribute - field to be returned
	*/
	public function getEntryAttribute($entry_id, $entries, $attribute){
		foreach($entries as $entry){
			if($entry['id'] == $entry_id){ 
				return $entry[$attribute];
			}
		}
	}

	/*
	*	Stores one staffplan entry in the database
	*/
	public function storeStaffplanEvent($user, $client, $project, $eventtype, $value, $timestamp){
		$stored = Measurement::createMeasurement(
			$user,
			$eventtype,
			'staffplan',
			$value,
			$timestamp,
			array( 'project' => $project, 'client' => $client)
		);
		return $stored;
	}

	/*
	*
	*/
	public function storeAssignment($assignment){
		// get user email
		$userEmail = $this->getEntryAttribute($assignment['user_id'], $this->users, 'email');
		// get project
		$project = $this->getEntryAttribute($assignment['project_id'], $this->projects, 'name');
		// get client
		$client = $this->getEntryAttribute($assignment['client_id'], $this->clients, 'name');
		
		// get array of work weeks (each entry in the array represents 1 week)
		// each week reports actual and planned hours for that assignment
		$weeks = $assignment['work_weeks'];

		foreach($weeks as $week){
			$date = $week['beginning_of_week'];

			// Jen's bug, entries dated in unix epoch 0 -> (early 1970)
			// do not accept any dates before 2010 as they are not valid
			if($date < 1262304000000) continue;

			// parse timestamp
			$timestamp = DateTime::createFromFormat('U', $date/1000);
			// skip null dates
			if (gettype($timestamp) != 'object') continue;

			// get estimated and actual hours
			$estimated = $week['estimated_hours'];
			$actual = $week['actual_hours'];

			// store estimated hours
			if(gettype($estimated) != 'NULL') {
				$this->storeStaffplanEvent(
					$userEmail, 
					$client,
					$project, 
					'Estimated work hours', 
					$estimated, 
					$timestamp
				);
			}

			// store actual hours
			if(gettype($actual) != 'NULL') {
				$this->storeStaffplanEvent(
					$userEmail, 
					$client,
					$project, 
					'Actual work hours', 
					$actual, 
					$timestamp
				);
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
		$this->info('Starting StaffplanWorker');

		// get data
		$this->info('Getting Staffplan data');
		$data = $this->getStaffplanData();

		// keep a 'user' array that will be used to link
		// a staffplan id to user emails.
		// only look for users that are known to invoLive
		$this->users = $this->filterUsers($data['users']);
		$this->info('Saving staffplans for '.count($this->users).' users.');

		$this->projects = $data['projects'];
		$this->clients = $data['clients'];

		// get all assignments
		$assignments = $data['assignments'];

		$saved = 0;
		$skipped = 0;

		// store each assignent data
		foreach($assignments as $assignment){
			if(in_array($assignment['user_id'], $this->validUIDs)){
				$this->storeAssignment($assignment);
				$saved += 1;
			} else {
				$skipped += 1;
			}
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