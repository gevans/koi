<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package   Koi
 */
abstract class Kohana_Koi_Gateway {

	/**
	 * @var  string  Format of amounts used by gateway
	 */
	protected static $money_format = 'dollars';

	/**
	 * @var  string  Default transaction currency when none is provided
	 */
	protected static $default_currency = 'USD';

	/**
	 * @var  array  Card types supported by the gateway
	 */
	protected static $supported_card_types = array();

	/**
	 * @var  array  Countries supported by the gateway
	 */
	protected static $supported_countries = array();

	protected static $debit_cards = array('switch', 'solo');

	/**
	 * Checks if the gateway supports a specified credit card type.
	 *
	 * @param   object|string  $card  [Koi_Credit_Card] or card type
	 * @return  boolean
	 */
	public static function supports_card($card)
	{
		return in_array(($card instanceof Koi_Credit_Card) ? $card->type : $card, static::$supported_card_types);
	}

	public static function supported_card_types()
	{
		return static::$supported_card_types;
	}

	/**
	 * Checks if the gateway supports a specified country.
	 *
	 * @param   string  $country  Country
	 * @return  boolean
	 */
	public static function supports_country($country)
	{
		return in_array($country, static::$supported_countries);
	}

	public static function supported_countries()
	{
		return static::$supported_countries;
	}

	/**
	 * Initializes a new gateway.
	 *
	 * @param  array  $options  Gateway options
	 */
	abstract public function __construct(array $options = array());

	/**
	 * Detects the base class name, without the `Koi_Gateway_` prefix.
	 *
	 * @return  string  Base class name
	 */
	protected function name()
	{
		return substr(get_class($this), 12);
	}

	/**
	 * Converts cents to the format required by the gateway.
	 *
	 * @param   integer|string  $cents  Amount in cents
	 * @return  string  Amount in cents or dollars
	 */
	protected function amount($cents)
	{
		if ($cents === NULL)
		{
			return NULL;
		}

		if ( ! is_int($cents) OR $cents < 0)
		{
			throw new Koi_Exception('Money amount must be a positive integer in cents.');
		}

		if (static::$money_format == 'cents')
		{
			return (string) $cents;
		}
		else
		{
			return sprintf('%.2f', $cents / 100);
		}
	}

	protected function requires_start_date_or_issue_number($credit_card)
	{
		return in_array($credit_card->type, static::$debit_cards);
	}

	protected function requires()
	{
		throw new Koi_Exception('Not implemented.');
	}

} // End Koi_Gateway