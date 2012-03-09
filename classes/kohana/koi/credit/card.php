<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * The `Koi_Credit_Card` class provides helpers for validating credit card info,
 * determining a card's type (Visa, MasterCard, etc.), and re
 *
 *     $credit_card = Koi::credit_card($this->request->post('credit_card'));
 *
 *     if ($credit_card->is_valid())
 *     {
 *         ORM::factory('credit_card')
 *             ->values($credit_card->as_array())
 *             ->create();
 *     }
 *     else
 *     {
 *         // This card is invalid or expired.
 *     }
 *
 * @package   Koi
 * @category  Credit Card
 */
class Kohana_Koi_Credit_Card {

	/**
	 * @var  boolean  Require verification values?
	 */
	public static $requires_verification_value = TRUE;

	/**
	 * @var  array  Credit card attributes
	 */
	protected $_attributes = array(
		'number'             => NULL,
		'month'              => NULL,
		'year'               => NULL,
		'type'               => NULL,
		'first_name'         => NULL,
		'last_name'          => NULL,
		'verification_value' => NULL,
		'start_month'        => NULL,
		'start_year'         => NULL,
		'issue_number'       => NULL,
	);

	/**
	 * @var  Validation
	 */
	protected $_validation;

	/**
	 * Creates a new `Koi_Credit_Card`.
	 *
	 * @param   array  $attributes  Credit card attributes
	 * @return  void
	 */
	public function __construct(array $attributes = array())
	{
		$this->_attributes = Arr::overwrite($this->_attributes, $attributes);

		if (isset($attributes['name']))
		{
			$this->name($attributes['name']);
		}
	}

	/**
	 * Sets and gets full names. Splits to first and last names.
	 *
	 * @param   string  $value  Full name
	 * @return  string  Full name
	 */
	public function name($value = NULL)
	{
		if ($value !== NULL)
		{
			// Split a full name into first and last names
			$names = explode(' ', $value);
			$this->_attributes['last_name']  = array_pop($names);
			$this->_attributes['first_name'] = implode($names, ' ');
		}

		// Combine first and last names
		return implode(array_filter(array($this->_attributes['first_name'], $this->_attributes['last_name']), 'mb_strlen'), ' ');
	}

	/**
	 * Tests if a first name is attached to credit card.
	 *
	 * @return  boolean
	 */
	public function has_first_name()
	{
		return isset($this->first_name);
	}

	/**
	 * Tests if a last name is attached to credit card.
	 *
	 * @return  boolean
	 */
	public function has_last_name()
	{
		return isset($this->last_name);
	}

	/**
	 * Tests if a name is attached to credit card.
	 *
	 * @return  boolean
	 */
	public function has_name()
	{
		return ($this->has_first_name() OR $this->has_last_name());
	}

	/**
	 * Gets last four digits of card number.
	 *
	 * @return  string
	 */
	public function last_digits()
	{
		return Koi::last_digits($this->number);
	}

	/**
	 * Gets first four digits of card number.
	 *
	 * @return  string
	 */
	public function first_digits()
	{
		return Koi::first_digits($this->number);
	}

	/**
	 * Gets masked card number.
	 *
	 *     Koi::credit_card(array('number' => '4242424242424242'))
	 *         ->display_number(); // => XXXX-XXXX-XXXX-4242
	 *
	 * @return [type]
	 */
	public function display_number()
	{
		return Koi::mask($this->number);
	}

	/**
	 * Handles retrieval of all credit card attributes.
	 *
	 * @param   string  $key  Attribute name
	 * @return  mixed
	 */
	public function __get($key)
	{
		// Alias type as brand
		if ($key == 'brand')
		{
			$key = 'type';
		}

		if ($key == 'name')
		{
			return $this->name();
		}
		elseif (array_key_exists($key, $this->_attributes))
		{
			return $this->_attributes[$key];
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Credit_Card class',
				array(':property' => $key));
		}
	}

	/**
	 * Sets credit card attributes.
	 *
	 * @param   string  $key    Attribute name
	 * @param   string  $value  Attribute value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		// Clear previously executed validations
		$this->_validation = NULL;

		// Alias type as brand
		if ($key == 'brand')
		{
			$key = 'type';
		}

		if ($key == 'number')
		{
			$this->_attributes['number'] = preg_replace('/[^\d]+/', '', $value);
		}
		elseif ($key == 'name')
		{
			$this->name($value);
		}
		elseif (array_key_exists($key, $this->_attributes))
		{
			$this->_attributes[$key] = $value;
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Credit_Card class',
				array(':property' => $key));
		}
	}

	/**
	 * Checks if credit card attributes are set.
	 *
	 * @param   string  $key  Attribute name
	 * @return  boolean
	 */
	public function __isset($key)
	{
		// Alias type as brand
		if ($key == 'brand')
		{
			$key = 'type';
		}

		if ($key == 'name')
		{
			return (isset($this->_attributes['first_name']) AND isset($this->_attributes['last_name']));
		}
		else
		{
			return isset($this->_attributes[$key]);
		}
	}

	/**
	 * Unsets credit card attributes.
	 *
	 * @param   string  $key  Attribute name
	 * @return  void
	 */
	public function __unset($key)
	{
		// Clear previously executed validations
		$this->_validation = NULL;

		// Alias type as brand
		if ($key == 'brand')
		{
			$key = 'type';
		}

		if ($key == 'name')
		{
			$this->_attributes['first_name'] = NULL;
			$this->_attributes['last_name']  = NULL;
		}
		elseif (array_key_exists($key, $this->_attributes))
		{
			$this->_attributes[$key] = NULL;
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Credit_Card class',
				array(':property' => $key));
		}
	}

	/**
	 * Displays a masked credit card number when converted to a string.
	 *
	 *     echo (string) $credit_card; // => XXXX-XXXX-XXXX-5678
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->display_number();
	}

	/**
	 * Returns credit card information as an array.
	 *
	 * @return  array
	 */
	public function as_array()
	{
		$this->run_filters();

		$card = Arr::extract($this->_attributes, array('number', 'month', 'year', 'type', 'first_name', 'last_name', 'verification_value'));

		if ($this->type == 'switch' OR $this->type == 'solo')
		{
			$card = Arr::merge($card, Arr::extract($this->_attributes, array('start_month', 'start_year', 'issue_number')));
		}

		return $card;
	}

	/**
	 * Runs filters on attributes.
	 *
	 * @return  void
	 */
	protected function run_filters()
	{
		$card = array(
			'number'             => $this->number,
			'month'              => ($this->month === NULL) ? NULL : (int) $this->month,
			'year'               => ($this->year === NULL) ? NULL : (int) $this->year,
			'type'               => $this->type,
			'first_name'         => $this->first_name,
			'last_name'          => $this->last_name,
			'verification_value' => $this->verification_value,
			'start_month'        => ($this->start_month !== NULL) ? (int) $this->start_month : $this->start_month,
			'start_year'         => ($this->start_year !== NULL) ? (int) $this->start_year : $this->start_year,
			'issue_number'       => $this->issue_number,
		);

		if (Koi_Valid::test_mode_card_number($this->number))
		{
			$this->type = $card['type'] = 'bogus';
		}
		else
		{
			$card['number'] = preg_replace('/[^\d]/', '', $this->number);
		}

		if ($this->type !== NULL)
		{
			$card['type'] = strtolower($this->type);
		}
		else
		{
			$card['type'] = Koi_Valid::type($this->number);
		}

		$this->_attributes = $card;
	}

	/**
	 * Tests if credit card is valid.
	 *
	 * @return  boolean
	 */
	public function is_valid()
	{
		return $this->validate()->check();
	}

	/**
	 * Gets or creates a validation object for the credit card.
	 *
	 * @uses    Validation::factory
	 * @return  Validation
	 */
	public function validate()
	{
		if ( ! $this->_validation)
		{
			$this->validate_essential_attributes();

			if ($this->type == 'bogus')
			{
				// Bogus cards, for testing purposes, should skip further validations
				return $this->_validation;
			}

			$this->validate_card_type();
			$this->validate_card_number();
			$this->validate_verification_value();
			$this->validate_switch_or_solo_attributes();
		}

		return $this->_validation;
	}

	/**
	 * Retrieves errors from the validation object.
	 *
	 * @param   string   $file
	 * @param   boolean  $translate
	 * @return  array
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		return $this->validate()->errors($file, $translate);
	}

	/**
	 * Adds the essential validations to the Validation object.
	 *
	 * @return  void
	 */
	protected function validate_essential_attributes()
	{
		$this->_validation = Validation::factory($this->as_array())
			->rule('first_name', 'not_empty')
			->rule('last_name', 'not_empty')
			->rules('month', array(
				array('not_empty'),
				array('Koi_Valid::month'),
			))
			->bind(':month', $this->month)
			->rules('year', array(
				array('not_empty'),
				array('Koi_Valid::expiry_year'),
				array('Koi_Valid::not_expired', array(':month', ':value'))
			));
	}

	/**
	 * Adds card type validations to the Validation object.
	 *
	 * @return  void
	 */
	protected function validate_card_type()
	{
		$this->_validation
			->bind(':number', $this->number)
			->rules('type', array(
				array('not_empty'),
				array('Koi_Valid::matches_type', array(':number', ':value')),
			));
	}

	/**
	 * Adds card number validations to the Validation object.
	 *
	 * @return  void
	 */
	protected function validate_card_number()
	{
		$this->_validation
			->rules('number', array(
				array('not_empty'),
				array('Koi_Valid::card_number'),
			));
	}

	/**
	 * Adds verification value validations to the Validation object.
	 *
	 * @return  void
	 */
	protected function validate_verification_value()
	{
		if (Koi_Credit_Card::$requires_verification_value)
		{
			$this->_validation->rule('verification_value', 'not_empty');
		}
	}

	/**
	 * Adds Switch / Solo attribute validations to the Validation object if the
	 * card type is `switch` or `solo`.
	 *
	 * @return  void
	 */
	protected function validate_switch_or_solo_attributes()
	{
		if ($this->type == 'switch' OR $this->type == 'solo')
		{
			if (Koi_Valid::month($this->start_month) AND Koi_Valid::start_year($this->start_year) OR Koi_Valid::issue_number($this->issue_number))
			{
				return;
			}

			$this->_validation
				->rule('start_month', 'Koi_Valid::month')
				->rule('start_year', 'Koi_Valid::start_year')
				->rule('issue_number', 'not_empty');
		}
	}

} // End Koi_Credit_Card