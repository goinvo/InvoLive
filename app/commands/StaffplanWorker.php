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
	protected $description = 'Command description.';

	private $users;
	private $projects; 
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

	public function deleteStaffplanEntries(){
		Measurement::where('source_id', Source::getId('staffplan'))->delete();
	}

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

	public function getUserAttribute($user_id, $attr){
		foreach($this->users as $user){
			if($user['id'] == $user_id){
				return $user[$attr];
			}
		}
		return null;
	}

	public function getProjectAttribute($project_id, $attr){
		foreach($this->projects as $project){
			if($project['id'] == $project_id){
				return $project[$attr];
			}
		}
		return null;
	}

	public function storeStaffplanEvent($user, $project, $eventtype, $value, $timestamp){
		$stored = Measurement::createMeasurement(
			$user,
			$eventtype,
			'staffplan',
			$value,
			$timestamp,
			false
		);
		if($stored['success']) {
			$stored['measurement']->addAttribute('project', $project);
		};
		return $stored;
	}

	public function storeAssignment($assignment){
		$userEmail = $this->getUserAttribute($assignment['user_id'], 'email');
		$project = $this->getProjectAttribute($assignment['project_id'], 'name');
		$weeks = $assignment['work_weeks'];
		foreach($weeks as $week){
			// do not accept any dates before 2010 as they are not valid
			$date = $week['beginning_of_week'];
			if($date < 1262304000000) continue;
			$timestamp = DateTime::createFromFormat('U', $date/1000);
			if (gettype($timestamp) != 'object'){
				continue;
			};

			$estimated = $week['estimated_hours'];
			$actual = $week['actual_hours'];
			// store estimated hours
			if(gettype($estimated) != 'NULL') {
				$this->storeStaffplanEvent(
					$userEmail, 
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
		$this->deleteStaffplanEntries();
		$data = $this->getStaffplanData();
		$this->users = $this->filterUsers($data['users']);
		$this->projects = $data['projects'];
		$assignments = $data['assignments'];

		$saved = 0;
		$invalid = 0;
		foreach($assignments as $assignment){
			if(in_array($assignment['user_id'], $this->validUIDs)){
				$this->storeAssignment($assignment);
			} else {
				$invalid += 1;
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