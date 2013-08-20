<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	// list of fields that could contain username
	public static $usernameFields = array('name', 'dropboxId', 'email');

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	protected $fillable = array('name');

	public $timestamps = false;

	/*
	* get user id from username
	*/
	public static function getId($name)
    {
    	// search for name in all possible username fields

    	foreach (self::$usernameFields as $field) {
	    	$result = User::where($field, $name)->first();

	    	if($result != null){
	    		return $result->id;
	    	}
	    }
    	return null;
    }

    public function getAvatar(){
    	return 'http://www.gravatar.com/avatar/'.md5($this->email);
    }


    /*
    *	User authorization subroutines
    */
    public function authorize($service){
    	$success = false;

    	if ($service == 'fitbit') {
    		/*
			*	Fitbit OAuth authorization subroutines
			*/
			
			$key = Config::get('live.fitbit-key');
		    $secret = Config::get('live.fitbit-secret');

		    // get tokens
		    $fitbit = new FitBitPHP($key, $secret);
		    $fitbit->initSession('http://'.$_SERVER['SERVER_NAME'].'/user/authorize?&service=fitbit&liveid='.$this->id);

		    // save tokens
		    if($fitbit->sessionStatus() == 2){
				$this->fitbitToken = $fitbit->getOAuthToken();
				$this->fitbitSecret = $fitbit->getOAuthSecret();
				$this->save();
				$success = true;
			}
    	} else if( $service == 'withings' ) {
			
			/*
			*	Withings OAuth authorization subroutines
			*/

			$key = Config::get('live.withings-key');
			$secret = Config::get('live.withings-secret');

			// get tokens
			$withings = new WithingsPHP($key, $secret);
			$withings->initSession('http://'.$_SERVER['SERVER_NAME'].'/user/authorize?&service=withings&liveid='.$this->id);

			// save tokens
			if($withings->sessionStatus() == 2){
				$this->withingsToken = $withings->getOAuthToken();
				$this->withingsSecret = $withings->getOAuthSecret();
				
				// withings callback returns withings id under param 'userid'
				$this->withingsId = $liveid = Input::get('userid');
				
				$this->save();
				$success = true;
			}

		} else if( $service == 'bodymedia'){

			/*
			*	Bodymedia OAuth authorization subroutines
			*/

			$key = Config::get('live.bodymedia-key');
			$secret = Config::get('live.bodymedia-secret');

			$bm = new BodymediaPHP($key, $secret);
			$bm->initSession('http://'.$_SERVER['SERVER_NAME'].'/user/authorize?&service=bodymedia&liveid='.$this->id);

			if($bm->sessionStatus() == 2){
				$this->bodymediaToken = $bm->getOAuthToken();
				$this->bodymediaSecret = $bm->getOAuthSecret();
				
				$this->save();
				$success = true;
			}

		} else {

		}

    	if ($success) {
			$msg =  ucfirst($service).' is now authorized for '.$this->name.'.';
			return View::make('msg', array('msg' => $msg));
		}
    }


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}




}