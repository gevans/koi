<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Koi AVS result class.
 *
 * @group koi
 * @group koi.credit_card
 * @group koi.validation
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_Result_AvsTest extends Koi_Unittest_TestCase {

	/**
	 * @test
	 * @return  void
	 */
	public function test_null()
	{
		$result = new Koi_Result_Avs(NULL);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_no_match()
	{
		$result = new Koi_Result_Avs(array('code' => 'N'));
		$this->assertEquals('N', $result->code);
		$this->assertEquals('N', $result->street_match);
		$this->assertEquals('N', $result->postal_match);
		$this->assertEquals(Arr::get(Koi_Result_Avs::messages(), 'N'), $result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_only_street_match()
	{
		$result = new Koi_Result_Avs(array('code' => 'A'));
		$this->assertEquals('A', $result->code);
		$this->assertEquals('Y', $result->street_match);
		$this->assertEquals('N', $result->postal_match);
		$this->assertEquals(Arr::get(Koi_Result_Avs::messages(), 'A'), $result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_only_postal_match()
	{
		$result = new Koi_Result_Avs(array('code' => 'W'));
		$this->assertEquals('W', $result->code);
		$this->assertEquals('N', $result->street_match);
		$this->assertEquals('Y', $result->postal_match);
		$this->assertEquals(Arr::get(Koi_Result_Avs::messages(), 'W'), $result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_null_data()
	{
		$result = new Koi_Result_Avs(array('code' => NULL));
		$this->assertNull($result->code);
		$this->assertNull($result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_empty_data()
	{
		$result = new Koi_Result_Avs(array('code' => ''));
		$this->assertNull($result->code);
		$this->assertNull($result->message);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_as_array()
	{
		$result = new Koi_Result_Avs(array('code' => 'X'));
		$avs_data = $result->as_array();
		$this->assertEquals('X', $avs_data['code']);
		$this->assertEquals(Arr::get(Koi_Result_Avs::messages(), 'X'), $avs_data['message']);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_street_match()
	{
		$result = new Koi_Result_Avs(array('street_match' => 'Y'));
		$this->assertEquals('Y', $result->street_match);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_postal_match()
	{
		$result = new Koi_Result_Avs(array('postal_match' => 'Y'));
		$this->assertEquals('Y', $result->postal_match);
	}

}