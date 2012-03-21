<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Result of **Card Verification Value** (CVV) check. Check additional codes
 * from [Cybersource](http://www.cybersource.com/support_center/support_documentation/quick_references/view.php?page_id=421)
 * website.
 *
 * @package   Koi
 * @category  Validation
 */
class Kohana_Koi_Result_CVV {

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
		return Koi_Result_CVV::$messages;
	}

	/**
	 * @var  string  Result code
	 */
	protected $code = NULL;

	/**
	 * @var  string  Result message
	 */
	protected $message = NULL;

	public function __construct($code)
	{
		$this->code    = ( ! empty($code)) ? strtoupper($code) : NULL;
		$this->message = Arr::get(Koi_Result_CVV::$messages, $code);
	}

	public function __get($key)
	{
		switch ($key)
		{
			case 'code':
				return $this->code;
			break;
			case 'message':
				return $this->message;
			break;
			default:
				throw new Kohana_Exception('The :property property does not exist in the Koi_Result_CVV class',
					array(':property' => $key));
			break;
		}
	}

	public function as_array()
	{
		return array(
			'code'    => $this->code,
			'message' => $this->message,
		);
	}

} // End Koi_Result_CVV