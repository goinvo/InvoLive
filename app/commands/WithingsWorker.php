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
		$key = Config::get('live.withings-key');
		$secret = Config::get('live.withings-secret');

		$token = '7d7dc0e51f7599201202b26510f3d10035f29edf3991716e8c5931496929f';
		$tokenSecret = 'e82300724f135d91172c5b2fdd1c1d0f48494b382c359b61c113506767e9c3';

		// $oauthObject = new OAuthSimple();

		// $signatures = array( 
		// 			'consumer_key' => $key,
  //                    'shared_secret'    => $secret,
  //                    'oauth_token' => $token,
  //                    'oauth_secret' => $tokenSecret);

		// //$oauthObject->setParameters( array('oauth_nonce' => 1234, 'oauth_timestamp' => 1375207876));

		// $result = $oauthObject->sign(array(
  //       'path'      =>'http://wbsapi.withings.net/v2/measure',
  //       'parameters'=> array('action'=>'getactivity', 'date' => '2013-07-29', 'userid'=> 2160884),
  //       'signatures'=> $signatures));

  //       echo $result['signed_url'];


		$w = new WithingsPHP($key, $secret);
		$w->setOAuthDetails($token, $tokenSecret);
		// $w->getProfile();

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