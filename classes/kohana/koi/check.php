<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Bank check helper.
 *
 * @package   Koi
 * @category  Helpers
 */
class Kohana_Koi_Check {

	/**
	 * @var  object  [Validation]
	 */
	protected $_validation;

	/**
	 * @var  array  Check attributes
	 */
	protected $_attributes = array(
		'first_name'          => NULL,
		'last_name'           => NULL,
		'routing_number'      => NULL,
		'account_number'      => NULL,
		'account_holder_type' => NULL,
		'account_type'        => NULL,
		'number'              => NULL,
		'institution_number'  => NULL,
		'transit_number'      => NULL,
	);

	/**
	 * Creates a new `Koi_Check`.
	 *
	 * @param   array  $attributes  Check attributes
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
		return implode(' ', array_filter(array($this->_attributes['first_name'], $this->_attributes['last_name']), 'mb_strlen'));
	}

	/**
	 * Gets or creates a validation object for the check.
	 *
	 * @uses    Validation::factory
	 * @return  Validation
	 */
	public function validate()
	{
		if ( ! $this->_validation)
		{
			$this->_validation = Validation::factory($this->_attributes)
				->rule('first_name', 'not_empty')
				->rule('last_name', 'not_empty')
				->rules('routing_number', array(
					array('not_empty'),
					array('Koi_Valid::routing_number'),
				))
				->rule('account_number', 'not_empty')
				->rules('account_holder_type', array(
					array('not_empty'),
					array('in_array', array(':value', array('personal', 'business'))),
				))
				->rule('account_type', 'in_array', array(':value', array('checking', 'savings')));

			$this->_validation->check();
		}

		return $this->_validation;
	}

	/**
	 * Tests if check is valid.
	 *
	 * @return  boolean
	 */
	public function is_valid()
	{
		return $this->validate()->check();
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

	public function type()
	{
		return 'check';
	}

	/**
	 * Handles retrieval of all check attributes.
	 *
	 * @param   string  $key  Attribute name
	 * @return  mixed
	 */
	public function __get($key)
	{
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
			throw new Kohana_Exception('The :property property does not exist in the Koi_Check class',
				array(':property' => $key));
		}
	}

	/**
	 * Sets check attributes.
	 *
	 * @param   string  $key    Attribute name
	 * @param   string  $value  Attribute value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		// Clear previously executed validations
		$this->_validation = NULL;

		if ($key == 'name')
		{
			$this->name($value);
		}
		elseif (array_key_exists($key, $this->_attributes))
		{
			$this->_attributes[$key] = $value;
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the Koi_Check class',
				array(':property' => $key));
		}
	}

	/**
	 * Checks if a check attribute is set.
	 *
	 * @param   string  $key  Attribute name
	 * @return  boolean
	 */
	public function __isset($key)
	{
		if ($key == 'name')
		{
			return (isset($this->_attributes['first_name']) OR isset($this->_attributes['last_name']));
		}
		else
		{
			return isset($this->_attributes[$key]);
		}
	}

	/**
	 * Unsets a check attribute.
	 *
	 * @param   string  $key  Attribute name
	 * @return  void
	 */
	public function __unset($key)
	{
		// Clear previously executed validations
		$this->_validation = NULL;

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
	 * Returns check information as an array.
	 *
	 *     $check->as_array();
	 *
	 * Returns an array similar to:
	 *
	 *     array(
	 *         'first_name'          => 'John',
	 *         'last_name'           => 'Wayne',
	 *         'routing_number'      => '111000025',
	 *         'account_number'      => '123456789012',
	 *         'account_holder_type' => 'personal',
	 *         'account_type'        => 'checking',
	 *         'number'              => '123',
	 *     );
	 *
	 * @return  array
	 */
	public function as_array()
	{
		$check = $this->_attributes;

		if ($check['institution_number'] === NULL AND $check['transit_number'] === NULL)
		{
			unset($check['institution_number'], $check['transit_number']);
		}

		return $check;
	}

} // End Koi_Check