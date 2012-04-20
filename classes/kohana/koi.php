<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package  Koi
 */
class Kohana_Koi {

	// Current Koi version
	const VERSION = '0.1.0';

	// Environment modes
	const PRODUCTION = 10;
	const TESTING    = 20;

	/**
	 * @var  integer  Current mode, defaults to `Koi::PRODUCTION`
	 */
	public static $mode = Koi::PRODUCTION;

	/**
	 * Instantiates a new [Koi_Gateway] for the specified gateway.
	 *
	 *     $gateway = Koi::gateway('authorizenet_cim'); // => Koi_Gateway_AuthorizeNet_CIM
	 *
	 * @param   string  $name     Gateway name
	 * @param   array   $options  Additional options
	 * @return  object  A subclass of [Koi_Gateway]
	 */
	public static function gateway($name, array $options = array())
	{
		$gateway = 'Koi_Gateway_'.ucfirst($name);
		return new $gateway($options);
	}

	/**
	 * Instantiates a new [Koi_Integration] for the specified integration.
	 *
	 *     $integration = Koi::integration('paypal'); // => Koi_Integration_PayPal
	 *
	 * @param   string  $name     Integration name
	 * @param   array   $options  Additional options
	 * @return  object  A subclass of [Koi_Integration]
	 */
	public static function integration($name, array $options = array())
	{
		$integration = 'Koi_Integration_'.ucfirst($name);
		return new $integration($options);
	}

	/**
	 * Creates a new [Koi_Credit_Card] instance for validating credit cards.
	 *
	 *     $credit_card = Koi::credit_card(array(
	 *         'first_name'         => 'Joe',
	 *         'last_name'          => 'Blow',
	 *         'number'             => '4111111111111111',
	 *         'month'              => '12',
	 *         'year'               => '2015',
	 *         'verification_value' => '123',
	 *     ));
	 *
	 *     if ($credit_card->is_valid())
	 *     {
	 *         // Do something.
	 *     }
	 *
	 * @chainable
	 * @param   array  $options  Card information
	 * @return  Koi_Credit_Card
	 */
	public static function credit_card(array $options = array())
	{
		return new Koi_Credit_Card($options);
	}

	/**
	 * Gets the first six digits of a card number.
	 *
	 * @param   string  $number  Number to slice
	 * @return  string
	 */
	public static function first_digits($number)
	{
		return substr($number, 0, 4);
	}

	/**
	 * Gets the last four digits of a card number.
	 *
	 * @param   string  $number  Number to slice
	 * @return  string
	 */
	public static function last_digits($number)
	{
		return (strlen($number <= 4)) ? $number : substr($number, -4);
	}

	/**
	 * Masks a credit card number, taking the last four digits and replacing
	 * the rest with Xs.
	 *
	 * @param   string  $number  Number to mask
	 * @return  string
	 */
	public static function mask($number)
	{
		return 'XXXX-XXXX-XXXX-'.Koi::last_digits($number);
	}

} // End Koi