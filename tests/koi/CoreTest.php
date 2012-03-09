<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Koi's core.
 *
 * @group koi
 * @group koi.core
 *
 * @package   Koi
 * @category  Tests
 */
class Koi_CoreTest extends Koi_Unittest_TestCase {

	/**
	 * Tests Koi::credit_card() returns an instance of Koi_Credit_Card.
	 *
	 * @return  void
	 */
	public function test_credit_card()
	{
		$this->assertTrue(Koi::credit_card() instanceof Koi_Credit_Card);
	}

	/**
	 * @test
	 * @return  void
	 */
	public function test_mask_number()
	{
		$this->assertEquals('XXXX-XXXX-XXXX-5100', Koi::mask('5105105105105100'));
	}

}