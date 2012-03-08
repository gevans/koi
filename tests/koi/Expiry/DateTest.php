<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests [Koi_Expiry_Date] class.
 *
 * @group koi
 * @group koi.credit_card
 *
 * @package    Koi
 * @category   Tests
 * @copyright  (c) 2012 Gabriel Evans
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT license
 */
class Koi_Expiry_DateTest extends Unittest_TestCase {

	/**
	 * Expiration dates in the past should be expired.
	 *
	 * @test
	 */
	public function test_should_be_expired()
	{
		$last_month = strtotime('last month');
		$date = new Koi_Expiry_Date(date('m', $last_month), date('Y', $last_month));
		$this->assertTrue($date->is_expired());
	}

	/**
	 * Expiration dates this month should not be expired.
	 *
	 * @test
	 */
	public function test_today_should_not_be_expired()
	{
		$date = new Koi_Expiry_Date(date('m'), date('Y'));
		$this->assertFalse($date->is_expired());
	}

	/**
	 * Expiration dates in the future should not be expired.
	 *
	 * @test
	 */
	public function test_dates_in_the_future_should_not_be_expired()
	{
		$next_month = strtotime('next month');
		$date = new Koi_Expiry_Date(date('m', $next_month), date('Y', $next_month));
		$this->assertFalse($date->is_expired());
	}

	/**
	 * Invalid expiration dates should have a timestamp of `0`.
	 *
	 * @test
	 */
	public function test_invalid_date()
	{
		$expiry = new Koi_Expiry_Date(13, 2009);
		$this->assertSame(0, $expiry->expiration());
	}

}