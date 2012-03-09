<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests the Arr lib that's shipped with kohana
 *
 * @group koi
 * @group koi.credit_card
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_Credit_CardTest extends Koi_Unittest_TestCase {

	protected $environmentDefault = array(
		'Koi::$mode' => Koi::TESTING,
		'Koi_Credit_Card::$requires_verification_value' => FALSE,
	);

	/**
	 * @var  Koi_Credit_Card
	 */
	protected $visa;

	/**
	 * @var  Koi_Credit_Card
	 */
	protected $solo;

	public function setUp()
	{
		parent::setUp();

		$this->visa = $this->credit_card('4779139500118580', array('type' => 'visa'));
		$this->solo = $this->credit_card('676700000000000000', array('type' => 'solo', 'issue_number' => '01'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_constructor_should_properly_assign_values()
	{
		$card = $this->credit_card();

		$this->assertEquals('4242424242424242', $card->number);
		$this->assertEquals(9, $card->month);
		$this->assertEquals(date('Y') + 1, $card->year);
		$this->assertEquals('Robert Paulson', $card->name);
		$this->assertEquals('visa', $card->type);
		$this->assertValid($card);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_new_credit_card_should_not_be_valid()
	{
		$card = new Koi_Credit_Card;

		$this->assertNotValid($card);
		$this->assertNotEmpty($card->errors());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_a_valid_visa_card()
	{
		$this->assertValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_a_valid_solo_card()
	{
		$this->assertValid($this->solo);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_cards_with_empty_names_should_not_be_valid()
	{
		$this->visa->first_name = '';
		$this->visa->last_name  = '';

		$this->assertNotValid($this->visa);
		$this->assertNotEmpty($this->visa->errors());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_liberate_a_bogus_card()
	{
		$card = $this->credit_card('', array('type' => 'bogus'));
		$this->assertValid($card);

		$card->type = 'visa';
		$this->assertNotValid($card);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_able_to_identify_invalid_card_numbers()
	{
		$this->visa->number = NULL;
		$this->assertNotValid($this->visa);

		$this->visa->number = '11112222333344ff';
		$this->assertNotValid($this->visa);

		$this->visa->number = '111122223333444';
		$this->assertNotValid($this->visa);

		$this->visa->number = '11112222333344444';
		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_invalid_when_type_cannot_be_detected()
	{
		$this->visa->number = NULL;
		$this->visa->type   = NULL;

		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_a_valid_card_number()
	{
		$this->visa->number = '4242424242424242';
		$this->assertValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_require_a_valid_card_month()
	{
		$this->visa->month = date('m');
		$this->visa->year  = date('Y');

		$this->assertValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_not_be_valid_with_empty_month()
	{
		$this->visa->month = '';
		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_not_be_valid_for_edge_month_cases()
	{
		$this->visa->month = 13;
		$this->visa->year  = date('Y');
		$this->assertNotValid($this->visa);

		$this->visa->month = 0;
		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_invalid_with_empty_year()
	{
		$this->visa->year = '';
		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_not_be_valid_for_edge_year_cases()
	{
		$this->visa->year = date('Y') - 1;
		$this->assertNotValid($this->visa);

		$this->visa->year = date('Y') + 21;
		$this->assertNotValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_a_valid_future_year()
	{
		$this->visa->year = date('Y') + 1;
		$this->assertValid($this->visa);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_valid_with_start_month_and_year_as_string()
	{
		$this->solo->start_month = '2';
		$this->solo->start_year  = '2007';
		$this->assertValid($this->solo);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_identify_wrong_cardtype()
	{
		$card = $this->credit_card(NULL, array('type' => 'master'));
		$this->assertNotValid($card);
	}

	/**
	 * Provides test data for test_should_display_number()
	 *
	 * @return  array
	 */
	public function provider_should_display_number()
	{
		return array(
			array('1111222233331234', 'XXXX-XXXX-XXXX-1234'),
			array('111222233331234',  'XXXX-XXXX-XXXX-1234'),
			array('1112223331234',    'XXXX-XXXX-XXXX-1234'),
			array(NULL,               'XXXX-XXXX-XXXX-'),
			array('',                 'XXXX-XXXX-XXXX-'),
			array('134',              'XXXX-XXXX-XXXX-134'),
			array('1234',             'XXXX-XXXX-XXXX-1234'),
			array('01234',            'XXXX-XXXX-XXXX-1234'),
		);
	}

	/**
	 * @test
	 * @dataProvider  provider_should_display_number
	 * @return  void
	 */
	public function test_should_display_number($number, $expected)
	{
		$this->assertEquals($expected, Koi::credit_card(array('number' => $number))->display_number());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_not_be_valid_when_requiring_a_verification_value()
	{
		Koi_Credit_Card::$requires_verification_value = TRUE;
		$card = $this->credit_card('4242424242424242', array('verification_value' => NULL));
		$this->assertNotValid($card);

		$card->verification_value = '123';
		$this->assertValid($card);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_require_valid_start_date_for_solo_or_switch()
	{
		$this->solo->start_month  = NULL;
		$this->solo->start_year   = NULL;
		$this->solo->issue_number = NULL;

		$this->assertNotValid($this->solo);

		$this->solo->start_month  = 2;
		$this->solo->start_year   = 2007;
		$this->assertValid($this->solo);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_require_a_valid_issue_number_for_solo_or_switch()
	{
		$this->solo->start_month  = NULL;
		$this->solo->start_year   = 2005;
		$this->solo->issue_number = NULL;

		$this->assertNotValid($this->solo);

		$this->solo->issue_number = 3;
		$this->assertValid($this->solo);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_return_last_four_digits_of_card_number()
	{
		$card = Koi::credit_card(array('number' => '4779139500118580'));
		$this->assertEquals('8580', $card->last_digits());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_bogus_last_digits()
	{
		$card = Koi::credit_card(array('number' => '1'));
		$this->assertEquals('1', $card->last_digits());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_return_first_four_digits_of_card_number()
	{
		$card = Koi::credit_card(array('number' => '4779139500118580'));
		$this->assertEquals('4779', $card->first_digits());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_return_first_bogus_digit_of_card_number()
	{
		$card = Koi::credit_card(array('number' => '1'));
		$this->assertEquals('1', $card->first_digits());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_true_when_credit_card_has_a_first_name()
	{
		$card = Koi::credit_card();
		$this->assertFalse($card->has_first_name());

		$card = Koi::credit_card(array('first_name' => 'James'));
		$this->assertTrue($card->has_first_name());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_be_true_when_credit_card_has_a_last_name()
	{
		$card = Koi::credit_card();
		$this->assertFalse($card->has_last_name());

		$card = Koi::credit_card(array('last_name' => 'Herdman'));
		$this->assertTrue($card->has_last_name());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_test_for_a_full_name()
	{
		$card = Koi::credit_card();
		$this->assertFalse($card->has_name());

		$card = Koi::credit_card(array('first_name' => 'James', 'last_name' => 'Herdman'));
		$this->assertTrue($card->has_name());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_handle_full_name_when_first_or_last_is_missing()
	{
		$card = Koi::credit_card(array('name' => 'James'));
		$this->assertTrue($card->has_name());
		$this->assertEquals('James', $card->name);

		$card = Koi::credit_card(array('name' => 'Herdman'));
		$this->assertTrue($card->has_name());
		$this->assertEquals('Herdman', $card->name);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_should_assign_a_full_name()
	{
		$card = Koi::credit_card(array('name' => 'James Herdman'));
		$this->assertEquals('James', $card->first_name);
		$this->assertEquals('Herdman', $card->last_name);

		$card = Koi::credit_card(array('name' => 'Rocket J. Squirrel'));
		$this->assertEquals('Rocket J.', $card->first_name);
		$this->assertEquals('Squirrel', $card->last_name);

		$card = Koi::credit_card(array('name' => 'Twiggy'));
		$this->assertEquals('', $card->first_name);
		$this->assertEquals('Twiggy', $card->last_name);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_autodetection_of_credit_card_type()
	{
		$card = new Koi_Credit_Card(array('number' => '4242424242424242'));
		$card->is_valid();
		$this->assertEquals('visa', $card->type);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_card_type_should_not_be_autodetected_when_provided()
	{
		$card = new Koi_Credit_Card(array('number' => '4242424242424242', 'type' => 'master'));
		$card->is_valid();
		$this->assertEquals('master', $card->type);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_detecting_bogus_card()
	{
		$this->assertSame(Koi::TESTING, Koi::$mode);
		$card = $this->credit_card('1');
		$card->is_valid();
		$this->assertEquals('bogus', $card->type);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_validating_bogus_card()
	{
		$card = $this->credit_card('1', array('type' => NULL));
		$this->assertValid($card);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_strip_non_digit_characters()
	{
		$card = $this->credit_card('4242-4242      %%%%%%4242......4242');
		$this->assertValid($card);
		$this->assertEquals('4242424242424242', $card->number);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_before_validate_handles_blank_number()
	{
		$card = $this->credit_card(NULL);
		$this->assertNotValid($card);
		$this->assertSame('', $card->number);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_type_is_aliased_as_brand()
	{
		$this->assertEquals($this->visa->type, $this->visa->brand);
		$this->assertEquals($this->solo->type, $this->solo->brand);
	}

}