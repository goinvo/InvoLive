<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DropboxWorker extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'DropboxWorker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates data from Dropbox service.';


	private $convertDropboxEvents = array('added' => 'Files created', 'deleted' => 'Files deleted', 'renamed' => 'Files renamed', 'edited' => 'Files edited', 'moved' => 'Files moved');

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
	*	Extracts data from Dropbox RSS pages
	*	@params:
	*	$url - string containing the url to process
	*/
	private function getEventsFromRSS($url){
		$feedData = file_get_contents($url);
		$feed = new SimpleXmlElement($feedData);

		$events = array();

		// extract data from each RSS item
		foreach($feed->channel->item as $entry) {

			// get timestamp
			$timestamp = DateTime::createFromFormat('U',strtotime($entry->pubDate));

			// parse description html
			$html = $entry->description[0];
			$dom = new DOMDocument();
			$dom->loadHTML($html);

			// get two <a> tags containing folder and filename
			$tags = $dom->getElementsByTagName('a');
			$folder = $tags->item(0)->nodeValue;
			$file = $tags->item(1)->nodeValue;

			// there may be <a> third tag containing a link to additional
			// files that got created or modified

			// count additional actions the third <a> tag is found
			$moreFiles = 0;
			if($tags->item(2) != null){
				$moreFiles = intval(str_replace(array('more', 'files', ' '), '', $tags->item(2)->nodeValue));
			}

			// remove all the <a> tags after having extracted their contents
			for($i=$tags->length-1; $i>=0; $i--){
				$tag = $tags->item($i);
				$tag->parentNode->removeChild($tag);
			}

			// do further string cleaning before extracting person
			// and action data
			$userAction = $dom->documentElement->nodeValue;
			$replaced = str_replace(array('In','file', 'folder', 'the', 'and', ',','.', '  '," \r", " \r\n", " \n"), '', $userAction);
			$split = explode(' ', $replaced);

			// action is last string chunnk
			$action = trim(preg_replace('/\s\s+/', ' ',array_pop($split)));

			// username is all strings before action name
			// this takes care or variable user names lengths
			$user = implode(' ', $split);

			// add element to event array
			array_push($events, array(
				'folder' => $folder, 
				'file'=> $file,
				'event' => $action,
				'quantity' => 1 + $moreFiles,
				'user' => $user,
				'timestamp' => $timestamp)
			);

		}
		return $events;
	}

	private function storeDropboxEvent($entry){
		$stored = Measurement::createMeasurement(
			$entry['user'], 
			$this->convertDropboxEvents[$entry['event']],
			'dropbox',
			$entry['quantity'],
			$entry['timestamp'],
			array('filename' => $entry['file'])
		);

		return $stored;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->info('DropboxWorker started at '.\Carbon\Carbon::now()->toDateTimeString());
		$this->info('Fetching RSS entries');

		// get RSS data
		$url = Config::get('live.dropbox-url');

		$events = $this->getEventsFromRSS($url);
		$this->info('Fectched '.count($events).' RSS entries');

		// will be used to print summary
		$new = 0;
		$old = 0;
		$invalid = 0;

		// store fetched data
		foreach($events as $event){
			$result = $this->storeDropboxEvent($event);

			// check for error messages
			if($result['success']){
				$new += 1;
			} else {
				if($result['message'] == 'Duplicate.'){
					$old += 1;
				} else {
					$invalid += 1;
					$this->info('---');
					$this->info('Error in adding entry:');
					$this->info($result['message']);
					$this->info('---');
				}
			}
		}
		$this->info('Summary:');
		$this->info($new.' new entries added.');
		$this->info($old.' old entries skipped.');
		$this->info($invalid.' invalid entries skipped.');
		$this->info(' ');

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array();
			array('url', InputArgument::OPTIONAL, 'RSS feek url.', null),
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