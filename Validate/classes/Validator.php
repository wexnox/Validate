<?php

class Validator
{
	protected $db;

	protected $errorHandler;

	protected $items;

	protected $rules = ['required', 'minlength', 'maxlength', 'email', 'alnum', 'match', 'unique'];

	public $messages = [
		'required' => 'The :field field is required',
		'minlength' => 'The :field field must be a minimum of :satisfier length',
		'maxlength' => 'The :field field must be a maximum of :satisfier length',
		'email' => 'That is not an valid email address',
		'alnum' => 'The :field field can only contain letters !',
		'match' => 'The :field field must match the :satisfier field',
		'unique' => 'That :field is already taken'
	];

	public function __construct(Database $db, ErrorHandler $errorHandler)
	{
		$this->db = $db;
		$this->errorHandler = $errorHandler;
	}

	public function check($items, $rules)
	{
		$this->items = $items;

		foreach ($items as $item => $value) 
		{
			if(in_array($item, array_keys($rules)))
			{

				$this->validate([
					'field' => $item,
					'value' => $value,
					'rules' => $rules[$item]
				]);
			}	
		}

		return $this;
	}

	public function fails()
	{
		return $this->errorHandler->hasErrors();
	}

	public function errors()
	{
		return $this->errorHandler;
	}

	protected function validate($item)
	{
		$field = $item['field'];

		foreach ($item['rules'] as $rule => $satisfier)
		{
			if(in_array($rule, $this->rules))
			{
				if(!call_user_func_array([$this, $rule], [$field, $item['value'], $satisfier]))
				{
					$this->errorHandler->addError(
						str_replace([':field', ':satisfier'], [$field, $satisfier], $this->messages[$rule]), 
						$field
					);
				}
			}
		}
	}

	protected function required($field, $value, $satisfier)
	{
		return !empty(trim($value));
	}

	protected function minlength($field, $value, $satisfier)
	{
		return mb_strlen($value) >= $satisfier;
	}

	protected function maxlength($field, $value, $satisfier)
	{
		return mb_strlen($value) <= $satisfier;
	}

	protected function email($field, $value, $satisfier)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	protected function alnum($field, $value, $satisfier)
	{
		return ctype_alnum($value);
	}

	protected function match($field, $value, $satisfier)
	{
		return $value === $this->items[$satisfier];
	}

	protected function unique($field, $value, $satisfier)
	{
		return !$this->db->table($satisfier)->exists([
			$field => $vaule
		]);
	}
}

?>