<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Result of **Card Verification Value** (CVV) check. Check additional codes
 * from [Cybersource](http://www.cybersource.com/support_center/support_documentation/quick_references/view.php?page_id=421)
 * website.
 *
 * @package   Koi
 * @category  Validation
 */
class Kohana_Koi_Result_Cvv {

	protected static $messages = array(
		'D' => 'Suspicious transaction',
		'I' => 'Failed data validation check',
		'M' => 'Match',
		'N' => 'No Match',
		'P' => 'Not Processed',
		'S' => 'Should have been present',
		'U' => 'Issuer unable to process request',
		'X' => 'Card does not support verification',
		'1' => 'Not supported by processor or card type',
		'2' => 'Unrecognized result code',
		'3' => 'No result code returned by processor',
	);

	public static function messages()
	{
		return Koi_Result_Cvv::$messages;
	}

	protected $attributes = array();

	public function __construct($code)
	{
		$this->attributes = array(
			'code'    => ( ! empty($code)) ? strtoupper($code) : NULL,
			'message' => Arr::get(Koi_Result_Cvv::$messages, $code),
		);
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Result_CVV class',
				array(':property' => $key));
		}
	}

	public function as_array()
	{
		return $this->attributes;
	}

} // End Koi_Result_CVV