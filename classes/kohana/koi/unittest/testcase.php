<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A version of the stock PHPUnit testcase that includes some extra helpers
 * and default settings
 */
abstract class Kohana_Koi_Unittest_TestCase extends Kohana_Unittest_TestCase {

	protected function credit_card($number = '4242424242424242', array $options = array())
	{
		$defaults = array(
			'number'             => $number,
			'month'              => 9,
			'year'               => date('Y') + 1,
			'first_name'         => 'Robert',
			'last_name'          => 'Paulson' ,
			'verification_value' => '123',
			'type'               => 'visa',
		);

		return Koi::credit_card(Arr::merge($defaults, $options));
	}

	protected function address(array $options = array())
	{
		return Arr::merge(array(
			'name'     => 'Joe Blow',
			'address1' => '1234 My Street',
			'address2' => 'Apt 1',
			'company'  => 'Widgets Inc',
			'city'     => 'Ottawa',
			'state'    => 'ON',
			'zip'      => 'K1C2N6',
			'country'  => 'CA',
			'phone'    => '(555) 555-5555',
			'fax'      => '(555) 555-6666',
			), $options);
	}

	public function assertValid($validateable)
	{
		$this->assertTrue($validateable->is_valid(), 'Expected to be valid');
	}

	public function assertNotValid($validateable)
	{
		$this->assertFalse($validateable->is_valid(), 'Expected to not be valid');
	}

}
