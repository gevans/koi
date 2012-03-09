<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package  Koi
 */
abstract class Kohana_Koi {

	// Current Koi version
	const VERSION = '0.1.0';

	// Environment modes
	const PRODUCTION = 1;
	const TESTING    = 2;

	/**
	 * @var  integer  Current mode, defaults to `Koi::PRODUCTION`
	 */
	public static $mode = Koi::PRODUCTION;

} // End Koi