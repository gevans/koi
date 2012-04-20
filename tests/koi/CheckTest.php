<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Check class.
 *
 * @group  koi
 * @group  koi.helpers
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_CheckTest extends Koi_Unittest_TestCase {

	const VALID_ABA      = '111000025';
	const INVALID_ABA    = '999999999';
	const MALFORMED_ABA  = 'I like fish';
	const ACCOUNT_NUMBER = '123456789012';

	/**
	 * @test
	 * @return  void
	 */
	public function test_validation()
	{
		$check = Koi::check();
		$this->assertFalse($check->is_valid());
		$this->assertNotEmpty($check->errors());
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_first_name_last_name()
	{
		$check = Koi::check(array(
			'name' => 'Fred Bloggs',
		));
		$this->assertEquals('Fred', $check->first_name);
		$this->assertEquals('Bloggs', $check->last_name);
		$this->assertEquals('Fred Bloggs', $check->name);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_null_name()
	{
		$check = Koi::check(array(
			'name' => NULL,
		));
		$this->assertNull($check->first_name);
		$this->assertNull($check->last_name);
		$this->assertEquals('', $check->name);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_valid()
	{
		$check = Koi::check(array(
			'name'                => 'Fred Bloggs',
			'routing_number'      => self::VALID_ABA,
			'account_number'      => self::ACCOUNT_NUMBER,
			'account_holder_type' => 'personal',
			'account_type'        => 'checking',
		));

		$this->assertValid($check);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_invalid_routing_number()
	{
		$check = Koi::check(array('routing_number' => self::INVALID_ABA));

		$this->assertNotValid($check);
		$this->assertNotEmpty(Arr::get($check->errors(), 'routing_number'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_malformed_routing_number()
	{
		$check = Koi::check(array('routing_number' => self::MALFORMED_ABA));

		$this->assertNotValid($check);
		$this->assertNotEmpty(Arr::get($check->errors(), 'routing_number'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_account_holder_type()
	{
		$check = Koi::check();
		$check->account_holder_type = 'business';
		$this->assertNull(Arr::get($check->errors(), 'account_holder_type'));

		$check->account_holder_type = 'personal';
		$this->assertNull(Arr::get($check->errors(), 'account_holder_type'));

		$check->account_holder_type = 'pleasure';
		$this->assertNotEmpty(Arr::get($check->errors(), 'account_holder_type'));

		$check->account_holder_type = NULL;
		$this->assertNotEmpty(Arr::get($check->errors(), 'account_holder_type'));
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_account_type()
	{
		$check = Koi::check();
		$check->account_type = 'checking';
		$this->assertNull(Arr::get($check->errors(), 'account_type'));

		$check->account_type = 'savings';
		$this->assertNull(Arr::get($check->errors(), 'account_type'));

		$check->account_type = 'mattress';
		$this->assertNotEmpty(Arr::get($check->errors(), 'account_type'));

		$check->account_type = NULL;
		$this->assertNull(Arr::get($check->errors(), 'account_type'));
	}

}