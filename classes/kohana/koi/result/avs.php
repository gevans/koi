<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Implements the **Address Verification System** (AVS).
 *
 * ### Useful Resources
 *
 *  * <https://www.wellsfargo.com/downloads/pdf/biz/merchant/visa_avs.pdf>
 *  * <http://en.wikipedia.org/wiki/Address_Verification_System>
 *  * <http://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_IG/html/app_avs_cvn_codes.htm#app_AVS_CVN_codes_7891_48375>
 *  * <http://imgserver.skipjack.com/imgServer/5293710/AVS%20and%20CVV2.pdf>
 *  * <http://www.emsecommerce.net/avs_cvv2_response_codes.htm>
 *
 * @package   Koi
 * @category  Validation
 */
class Kohana_Koi_Result_Avs {

	protected static $messages = array(
		'A' => 'Street address matches, but 5-digit and 9-digit postal code do not match.',
		'B' => 'Street address matches, but postal code not verified.',
		'C' => 'Street address and postal code do not match.',
		'D' => 'Street address and postal code match.',
		'E' => 'AVS data is invalid or AVS is not allowed for this card type.',
		'F' => 'Card member\'s name does not match, but billing postal code matches.',
		'G' => 'Non-U.S. issuing bank does not support AVS.',
		'H' => 'Card member\'s name does not match. Street address and postal code match.',
		'I' => 'Address not verified.',
		'J' => 'Card member\'s name, billing address, and postal code match. Shipping information verified and chargeback protection guaranteed through the Fraud Protection Program.',
		'K' => 'Card member\'s name matches but billing address and billing postal code do not match.',
		'L' => 'Card member\'s name and billing postal code match, but billing address does not match.',
		'M' => 'Street address and postal code match.',
		'N' => 'Street address and postal code do not match.',
		'O' => 'Card member\'s name and billing address match, but billing postal code does not match.',
		'P' => 'Postal code matches, but street address not verified.',
		'Q' => 'Card member\'s name, billing address, and postal code match. Shipping information verified but chargeback protection not guaranteed.',
		'R' => 'System unavailable.',
		'S' => 'U.S.-issuing bank does not support AVS.',
		'T' => 'Card member\'s name does not match, but street address matches.',
		'U' => 'Address information unavailable.',
		'V' => 'Card member\'s name, billing address, and billing postal code match.',
		'W' => 'Street address does not match, but 9-digit postal code matches.',
		'X' => 'Street address and 9-digit postal code match.',
		'Y' => 'Street address and 5-digit postal code match.',
		'Z' => 'Street address does not match, but 5-digit postal code matches.'
	);

	protected static $postal_match_codes = array(
		'D' => 'Y',
		'H' => 'Y',
		'F' => 'Y',
		'J' => 'Y',
		'L' => 'Y',
		'M' => 'Y',
		'P' => 'Y',
		'Q' => 'Y',
		'V' => 'Y',
		'W' => 'Y',
		'X' => 'Y',
		'Y' => 'Y',
		'Z' => 'Y',
		'A' => 'N',
		'C' => 'N',
		'K' => 'N',
		'N' => 'N',
		'O' => 'N',
		'G' => 'X',
		'S' => 'X',
		'B' => NULL,
		'E' => NULL,
		'I' => NULL,
		'R' => NULL,
		'T' => NULL,
		'U' => NULL,
	);

	protected static $street_match_codes = array(
		'A' => 'Y',
		'B' => 'Y',
		'D' => 'Y',
		'H' => 'Y',
		'J' => 'Y',
		'M' => 'Y',
		'O' => 'Y',
		'Q' => 'Y',
		'T' => 'Y',
		'V' => 'Y',
		'X' => 'Y',
		'Y' => 'Y',
		'C' => 'N',
		'K' => 'N',
		'L' => 'N',
		'N' => 'N',
		'W' => 'N',
		'Z' => 'N',
		'G' => 'X',
		'S' => 'X',
		'E' => NULL,
		'F' => NULL,
		'I' => NULL,
		'P' => NULL,
		'R' => NULL,
		'U' => NULL,
	);

	public static function messages()
	{
		return Koi_Result_Avs::$messages;
	}

	protected $attributes = array(
		'code'         => NULL,
		'message'      => NULL,
		'street_match' => NULL,
		'postal_match' => NULL,
	);

	public function __construct(array $attrs = NULL)
	{
		if ($attrs === NULL)
		{
			$attrs = array();
		}

		if (isset($attrs['code']) AND ! empty($attrs['code']))
		{
			$this->attributes['code'] = strtoupper($attrs['code']);
		}

		$this->attributes['message'] = Arr::get(Koi_Result_Avs::$messages, $this->code);

		if ( ! isset($attrs['street_match']) OR empty($attrs['street_match']))
		{
			$this->attributes['street_match'] = Arr::get(Koi_Result_Avs::$street_match_codes, $this->code);
		}
		else
		{
			$this->attributes['street_match'] = strtoupper($attrs['street_match']);
		}

		if ( ! isset($attrs['postal_match']) OR empty($attrs['postal_match']))
		{
			$this->attributes['postal_match'] = Arr::get(Koi_Result_Avs::$postal_match_codes, $this->code);
		}
		else
		{
			$this->attributes['postal_match'] = strtoupper($attrs['postal_match']);
		}
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

} // End Koi_Result_AVS