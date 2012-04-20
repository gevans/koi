<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Helper class for credit card expiration dates.
 *
 *     $expiration = new Koi_Expiry_Date(1, 2012);
 *     $expiration->is_expired() // => TRUE
 *
 * [!!] This class should not be used directly. The [Koi_Credit_Card] class should be used instead.
 *
 * @package   Koi
 * @category  Credit Card
 */
class Kohana_Koi_Expiry_Date {

	/**
	 * @var  integar  Expiration month
	 */
	protected $month = 0;

	/**
	 * @var  integer  Expiration year
	 */
	protected $year = 0;

	/**
	 * Creates a new `Koi_Expiry_Date`.
	 *
	 * @param  integer  $month  Expiration month
	 * @param  integer  $year   Expiration year
	 */
	public function __construct($month = 0, $year = 0)
	{
		$this->month = (int) $month;
		$this->year  = (int) $year;
	}

	/**
	 * Gets a unix timestamp of the expiration date.
	 *
	 * @return  integer  Unix timestamp
	 */
	public function expiration()
	{
		return (checkdate($this->month, 1, $this->year)) ? mktime(23, 59, 59, $this->month, $this->month_days(), $this->year) : 0;
	}

	/**
	 * Determines whether the date has expired.
	 *
	 * @return  boolean
	 */
	public function is_expired()
	{
		return (time() > $this->expiration());
	}

	/**
	 * Handles retrieval of expiry date attributes.
	 *
	 * @param   string  $key  Attribute name
	 * @return  mixed
	 */
	public function __get($key)
	{
		if ($key == 'expiration')
		{
			return $this->expiration();
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Expiry_Date class',
				array(':property' => $key));
		}
	}

	/**
	 * Gets the number of days in the expiration's month.
	 *
	 * @return  integer  Days in calendar month
	 */
	protected function month_days()
	{
		if (function_exists('cal_days_in_month'))
		{
			return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
		}
		else
		{
			// see http://php.net/manual/en/function.cal-days-in-month.php#104427
			return date('t', mktime(0, 0, 0, $this->month, 1, $this->year));
		}
	}

} // End Koi_Expiry_Date