<?php
/**
 * Auto generated by prado-cli.php on 2007-04-07 10:44:25.
 */
class UserRecord extends TActiveRecord
{
	const TABLE='users';

	public $username;
	public $email;
	public $password;
	public $role;
	public $first_name;
	public $last_name;

	public $posts=array();

	public static $RELATIONS=array
	(
		'posts' => array(self::HAS_MANY, 'PostRecord'),
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>