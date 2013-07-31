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

    /*
	* create new user account
	*/
    public static function createUser($name){
    	if(self::getId($name) != null) {
    		return False;
    	}

    	$user = new User;
    	$user->name = $name;
		$user->save();
		return True;
    }

    public function getAvatar(){
    	return 'http://www.gravatar.com/avatar/'.md5($this->email);
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