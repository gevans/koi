<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Koi validation methods.
 *
 * @group koi
 * @group koi.credit_card
 * @group koi.validation
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_ValidTest extends Koi_Unittest_TestCase {

	/**
	 * Tests Koi_Valid::expiry_month()
	 *
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_identify_valid_months()
	{
		$this->assertFalse(Koi_Valid::month(-1));
		$this->assertFalse(Koi_Valid::month(13));
		$this->assertFalse(Koi_Valid::month(NULL));
		$this->assertFalse(Koi_Valid::month(''));

		foreach (range(1, 12) as $n)
		{
			$this->assertTrue(Koi_Valid::month($n));
		}
	}

	/**
	 * Tests Koi_Valid::expiry_year()
	 *
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_identify_valid_expiry_years()
	{
		$this->assertFalse(Koi_Valid::expiry_year(-1));
		$this->assertFalse(Koi_Valid::expiry_year(date('Y') + 21));

		$this->assertTrue(Koi_Valid::expiry_year(date('Y')));

		foreach (range(date('Y'), date('Y') + 20) as $n)
		{
			$this->assertTrue(Koi_Valid::expiry_year($n));
		}
	}

	/**
	 * Tests Koi_Valid::start_year()
	 *
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_identify_valid_start_years()
	{
		$this->assertTrue(Koi_Valid::start_year(1988));
		$this->assertTrue(Koi_Valid::start_year(2007));
		$this->assertTrue(Koi_Valid::start_year(3000));

		$this->assertFalse(Koi_Valid::start_year(1987));
	}

	/**
	 * Tests Koi_Valid::start_year() can handle strings
	 *
	 * @test
	 * @return  void
	 */
	public function test_valid_start_year_can_handle_strings()
	{
		$this->assertTrue(Koi_Valid::start_year('2009'));
	}

	/**
	 * Tests Koi_Valid::month() can handle strings
	 *
	 * @test
	 * @return  void
	 */
	public function test_valid_month_can_handle_strings()
	{
		$this->assertTrue(Koi_Valid::month('1'));
	}

	/**
	 * Tests Koi_Valid::expiry_year() can handle strings
	 *
	 * @test
	 * @return  void
	 */
	public function test_valid_expiry_year_can_handle_strings()
	{
		$this->assertTrue(Koi_Valid::expiry_year(strval(date('Y') + 1)));
	}

	/**
	 * Tests Koi_Valid::issue_number()
	 *
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_identify_valid_issue_numbers()
	{
		$this->assertTrue(Koi_Valid::issue_number(1));
		$this->assertTrue(Koi_Valid::issue_number(10));
		$this->assertTrue(Koi_Valid::issue_number('12'));
		$this->assertTrue(Koi_Valid::issue_number(0));

		$this->assertFalse(Koi_Valid::issue_number(-1));
		$this->assertFalse(Koi_Valid::issue_number(123));
		$this->assertFalse(Koi_Valid::issue_number('CAT'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_correctly_identify_card_type()
	{
		$this->assertEquals('visa', Koi_Valid::type('4242424242424242'));
		$this->assertEquals('american_express', Koi_Valid::type('341111111111111'));
		$this->assertNull(Koi_Valid::type(''));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_dankort_card_type()
	{
		$this->assertEquals('dankort', Koi_Valid::type('5019717010103742'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_visa_dankort_as_visa()
	{
		$this->assertEquals('visa', Koi_Valid::type('4571100000000000'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_electron_dk_as_visa()
	{
		$this->assertEquals('visa', Koi_Valid::type('4175001000000000'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_diners_club()
	{
		$this->assertEquals('diners_club', Koi_Valid::type('36148010000000'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_diners_club_dk()
	{
		$this->assertEquals('diners_club', Koi_Valid::type('30401000000000'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_maestro_dk_as_maestro()
	{
		$this->assertEquals('maestro', Koi_Valid::type('6769271000000000'));
	}

	/**
	 * Provides Maestro card numbers for test_should_detect_maestro_cards()
	 *
	 * @return  array
	 */
	public function provider_maestro_card_numbers()
	{
		return array(
			array('5000000000000000'),
			array('5099999999999999'),
			array('5600000000000000'),
			array('5899999999999999'),
			array('6000000000000000'),
			array('6999999999999999'),
			array('6761999999999999'),
			array('6763000000000000'),
			array('5038999999999999'),
			array('5020100000000000'),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_maestro_card_numbers
	 * @return        void
	 */
	public function test_should_detect_maestro_cards($number)
	{
		$this->assertEquals('maestro', Koi_Valid::type($number));
	}

	/**
	 * Provides non-Maestro card numbers for test_should_not_detect_non_maestro_cards()
	 *
	 * @return  array
	 */
	public function provider_non_maestro_card_numbers()
	{
		return array(
			array('4999999999999999'),
			array('5100000000000000'),
			array('5599999999999999'),
			array('5900000000000000'),
			array('5999999999999999'),
			array('7000000000000000'),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_non_maestro_card_numbers
	 * @return        void
	 */
	public function test_should_not_detect_non_maestro_cards($number)
	{
		$this->assertNotEquals('maestro', Koi_Valid::type($number));
	}

	/**
	 * Provides MasterCard card numbers for test_should_detect_mastercard()
	 *
	 * @return  array
	 */
	public function provider_mastercard_card_numbers()
	{
		return array(
			array('6771890000000000'),
			array('5413031000000000'),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_mastercard_card_numbers
	 * @return        void
	 */
	public function test_should_detect_mastercard($number)
	{
		$this->assertEquals('master', Koi_Valid::type($number));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_forbrugsforeningen()
	{
		$this->assertEquals('forbrugsforeningen', Koi_Valid::type('6007221000000000'));
	}

	/**
	 * Provides Laser card numbers for test_should_detect_laser_card()
	 *
	 * @return  array
	 */
	public function provider_should_detect_laser_card()
	{
		return array(
			// 16 digits
			array('6304985028090561', TRUE),
			// 18 digits
			array('630498502809056151', TRUE),
			// 19 digits
			array('6304985028090561515', TRUE),
			// 17 digits
			array('63049850280905615', FALSE),
			// 15 digits
			array('630498502809056', FALSE),
			// Alternate format
			array('6706950000000000000', TRUE),
			// Alternate format (16 digits)
			array('6706123456789012', TRUE),
			// New format (16 digits)
			array('6709123456789012', TRUE),
			// Ulster bank (Ireland) with 12 digits
			array('677117111234', TRUE),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_should_detect_laser_card
	 * @return  void
	 */
	public function test_should_detect_laser_card($number, $should_be_laser)
	{
		if ($should_be_laser)
		{
			$this->assertEquals('laser', Koi_Valid::type($number));
		}
		else
		{
			$this->assertNotEquals('laser', Koi_Valid::type($number));
		}
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_detect_when_an_argument_type_does_not_match_calculated_type()
	{
		$this->assertTrue(Koi_Valid::matches_type('4175001000000000', 'visa'));
		$this->assertFalse(Koi_Valid::matches_type('4175001000000000', 'master'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_detecting_full_range_of_maestro_card_numbers()
	{
		$maestro = '50000000000';

		$this->assertEquals(11, strlen($maestro));
		$this->assertNotEquals('maestro', Koi_Valid::type($maestro));

		while (strlen($maestro) < 19)
		{
			$maestro .= '0';
			$this->assertEquals('maestro', Koi_Valid::type($maestro));
		}

		$this->assertEquals(19, strlen($maestro));

		$maestro .= '0';
		$this->assertNotEquals('maestro', Koi_Valid::type($maestro));
	}

	/**
	 * Provides Discover card numbers for test_should_detect_laser_card()
	 *
	 * @return  array
	 */
	public function provider_should_detect_discover_card()
	{
		return array(
			// Discover cards
			array('6011000000000000', TRUE),
			array('6500000000000000', TRUE),
			array('6221260000000000', TRUE),
			array('6450000000000000', TRUE),
			// Non-Discover cards
			array('6010000000000000', FALSE),
			array('6600000000000000', FALSE),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_should_detect_discover_card
	 * @return        void
	 */
	public function test_should_detect_discover_card($number, $should_be_discover)
	{
		if ($should_be_discover)
		{
			$this->assertEquals('discover', Koi_Valid::type($number));
		}
		else
		{
			$this->assertNotEquals('discover', Koi_Valid::type($number));
		}
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_16_digit_maestro_uk()
	{
		$number = '6759000000000000';
		$this->assertEquals(16, strlen($number));
		$this->assertEquals('switch', Koi_Valid::type($number));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_18_digit_maestro_uk()
	{
		$number = '675900000000000000';
		$this->assertEquals(18, strlen($number));
		$this->assertEquals('switch', Koi_Valid::type($number));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_19_digit_maestro_uk()
	{
		$number = '6759000000000000000';
		$this->assertEquals(19, strlen($number));
		$this->assertEquals('switch', Koi_Valid::type($number));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_not_be_expired()
	{
		$this->assertTrue(Koi_Valid::not_expired(date('m'), date('Y')));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_expired()
	{
		$last_month = strtotime('last month');
		$this->assertFalse(Koi_Valid::not_expired(date('m', $last_month), date('Y', $last_month)));
	}

	/**
	 * Provides test data to test_should_be_test_mode_card_number() and test_should_not_be_test_mode_card_number()
	 *
	 * @return  array
	 */
	public function provider_test_mode_card_number()
	{
		return array(
			array('1'),
			array('2'),
			array('3'),
			array('success'),
			array('failure'),
			array('error'),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_test_mode_card_number
	 * @return  void
	 */
	public function test_should_be_test_mode_card_number($number)
	{
		$this->assertTrue(Koi_Valid::test_mode_card_number($number));
		$this->assertEquals('bogus', Koi_Valid::type($number));
	}

	/**
	 * @test
	 * @dataProvider  provider_test_mode_card_number
	 * @return  void
	 */
	public function test_should_not_be_test_mode_card_number($number)
	{
		$old_mode = Koi::$mode;
		Koi::$mode = Koi::PRODUCTION;

		$this->assertFalse(Koi_Valid::test_mode_card_number($number));
		$this->assertNotEquals('bogus', Koi_Valid::type($number));

		Koi::$mode = $old_mode;
	}

	/**
	 * Provides test data for test_routing_number()
	 *
	 * @return  array
	 */
	public function provider_routing_number()
	{
		return array(
			array('111000025', TRUE),
			array('999999999', FALSE),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_routing_number
	 * @return  void
	 */
	public function test_routing_number($number, $expected)
	{
		$this->assertEquals(Koi_Valid::routing_number($number), $expected);
	}

}