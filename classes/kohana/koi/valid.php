<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Koi validation rules.
 *
 * @package   Koi
 * @category  Security
 */
class Kohana_Koi_Valid {

	/**
	 * @var  array  Credit card companies and regexps
	 */
	protected static $card_companies = array(
		'visa'               => '/^4\d{12}(\d{3})?$/D',
		'master'             => '/^(5[1-5]\d{4}|677189)\d{10}$/D',
		'discover'           => '/^(6011|65\d{2}|64[4-9]\d)\d{12}|(62\d{14})$/D',
		'american_express'   => '/^3[47]\d{13}$/D',
		'diners_club'        => '/^3(0[0-5]|[68]\d)\d{11}$/D',
		'jcb'                => '/^35(28|29|[3-8]\d)\d{12}$/D',
		'switch'             => '/^6759\d{12}(\d{2,3})?$/D',
		'solo'               => '/^6767\d{12}(\d{2,3})?$/D',
		'dankort'            => '/^5019\d{12}$/D',
		'maestro'            => '/^(5[06-8]|6\d)\d{10,17}$/D',
		'forbrugsforeningen' => '/^600722\d{10}$/D',
		'laser'              => '/^(6304|6706|6709|6771(?!89))\d{8}(\d{4}|\d{6,7})?$/D',
	);

	/**
	 * Gets an array of card companies and regexps from [Koi_Valid::$card_companies].
	 *
	 * @return  array
	 */
	public static function card_companies()
	{
		return Koi_Valid::$card_companies;
	}

	/**
	 * Tests if a month is valid.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function month($number)
	{
		return Valid::range($number, 1, 12);
	}

	/**
	 * Tests if a credit card expiry year is valid.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function expiry_year($year)
	{
		return Valid::range($year, date('Y'), date('Y') + 20);
	}

	/**
	 * Tests if a credit card start year is valid.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function start_year($year)
	{
		return (preg_match('/^\d{4}$/D', $year) AND $year > 1987);
	}

	/**
	 * Tests if an issue number is valid.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function issue_number($number)
	{
		return (bool) preg_match('/^\d{1,2}$/D', $number);
	}

	/**
	 * Tests if a card number length is valid.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function card_number_length($number)
	{
		return (strlen($number) >= 12);
	}

	/**
	 * Tests if a card number is a valid test mode number.
	 *
	 * [!!] Only valid when [Koi::$mode] is set to `Koi::TESTING`.
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function test_mode_card_number($number)
	{
		return (Koi::$mode === Koi::TESTING AND
			in_array($number, array('1', '2', '3', 'success', 'failure', 'error')));
	}

	/**
	 * Detects and returns a string containing the type of card using
	 * regular expressions.
	 *
	 * @param   string  $number  Number to detect
	 * @return  string  Detected card company name
	 */
	public static function type($number)
	{
		if (Koi_Valid::test_mode_card_number($number))
		{
			return 'bogus';
		}

		$card_companies = Koi_Valid::card_companies();

		foreach ($card_companies as $company => $pattern)
		{
			if ($company == 'maestro')
			{
				// Skip Maestro regexp which overlaps with the MasterCard regexp.
				continue;
			}

			if (preg_match($pattern, $number))
			{
				return $company;
			}
		}

		if (preg_match($card_companies['maestro'], $number))
		{
			return 'maestro';
		}

		// No card company was detected
		return NULL;
	}

	/**
	 * Tests if a card number matches a specified type.
	 *
	 * @param   string  $number  Number to check
	 * @param   string  $type    Type to check
	 * @return  boolean
	 */
	public static function matches_type($number, $type)
	{
		return (Koi_Valid::type($number) == $type);
	}

	/**
	 * Tests if a card number is a test mode card number, or of valid length
	 * and Luhn checksum (see [Valid::luhn]).
	 *
	 * @param   string  $number  Number to check
	 * @return  boolean
	 */
	public static function card_number($number)
	{
		return (Koi_Valid::test_mode_card_number($number) OR
			Koi_Valid::card_number_length($number) AND
			Valid::luhn($number));
	}

	/**
	 * Tests if a card month and year is expired.
	 *
	 * @param   string  $month  Month to check
	 * @param   string  $year   Year to check
	 * @return  boolean
	 */
	public static function not_expired($month, $year)
	{
		$date = new Koi_Expiry_Date($month, $year);

		return ( ! $date->is_expired());
	}

} // End Koi_Valid