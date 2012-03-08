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
		try
		{
			return mktime(23, 59, 59, $this->month, $this->month_days(), $this->year);
		}
		catch (ErrorException $e)
		{
			return 0;
		}
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
	 * Gets the number of days in the expiration's month.
	 *
	 * @return  integer  Days in calendar month
	 */
	protected function month_days()
	{
		return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
	}

} // End Koi_Expiry_Date