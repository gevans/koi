<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Koi CVV result class.
 *
 * @group koi
 * @group koi.credit_card
 * @group koi.validation
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_Result_CVVTest extends Koi_Unittest_TestCase {

	/**
	 * @test
	 * @return  void
	 */
	public function test_nil_data()
	{
		$result = new Koi_Result_CVV(NULL);
		$this->assertNull($result->code);
		$this->assertNull($result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_blank_data()
	{
		$result = new Koi_Result_CVV('');
		$this->assertNull($result->code);
		$this->assertNull($result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_successful_match()
	{
		$result = new Koi_Result_CVV('M');
		$this->assertEquals('M', $result->code);
		$this->assertEquals(Arr::get(Koi_Result_CVV::messages(), 'M'), $result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_failed_match()
	{
		$result = new Koi_Result_CVV('N');
		$this->assertEquals('N', $result->code);
		$this->assertEquals(Arr::get(Koi_Result_CVV::messages(), 'N'), $result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_as_array()
	{
		$result = new Koi_Result_CVV('M');
		$array  = $result->as_array();
		$this->assertEquals('M', $array['code']);
		$this->assertEquals(Arr::get(Koi_Result_CVV::messages(), 'M'), $array['message']);
	}

}