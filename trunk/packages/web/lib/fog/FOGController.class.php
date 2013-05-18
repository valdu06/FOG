<?php

// Blackout - 1:28 PM 23/09/2011
abstract class FOGController extends FOGBase
{
	// Table
	public $databaseTable = '';
	
	// Name -> Database field name
	public $databaseFields = array();
	
	// ->load() queries, this way subclasses can override (ie: NodeFailure)
	protected $loadQueryTemplateSingle = "SELECT * FROM `%s` WHERE `%s`='%s'";
	protected $loadQueryTemplateMultiple = "SELECT * FROM `%s` WHERE %s";
	
	// Do not update these database fields
	public $databaseFieldsToIgnore = array(
		'createdBy',
		'createdTime'
	);
	
	// Allow setting / getting of these additional fields
	public $additionalFields = array();
	
	// Required database fields
	public $databaseFieldsRequired = array();
	
	// Store data array
	protected $data = array();
	
	// Auto save class data on __destruct
	public $autoSave = false;
	
	// Debug & Info
	public $debug = true;
	public $info = true;
	
	// Database field to Class relationships
	public $databaseFieldClassRelationships = array();
	
	private $Manager;
	
	// Construct
	public function __construct($data)
	{
		// FOGBase contstructor
		parent::__construct();
		
		try
		{
			// Error checking
			if (!count($this->databaseFields))
			{
				throw new Exception('No database fields defined for this class!');
			}
			
			// Flip database fields and common name - used multiple times
			$this->databaseFieldsFlipped = array_flip($this->databaseFields);
			
			// Created By
			if (array_key_exists('createdBy', $this->databaseFields) && !empty($_SESSION['FOG_USERNAME']))
			{
				$this->set('createdBy', $_SESSION['FOG_USERNAME']);
			}
			
			// Add incoming data
			if (is_array($data))
			{
				// Iterate data -> Set data
				foreach ($data AS $key => $value)
				{
					$this->set($this->key($key), $value);
				}
			}
			// If incoming data is an INT -> Set as ID -> Load from database
			elseif (is_numeric($data))
			{
				if ($data === 0 || $data < 0)
				{
					throw new Exception(sprintf('No data passed, or less than zero, Value: %s', $data));
					//return false;
				}
				
				$this->set('id', $data)->load();
			}
			// Unknown data format
			else
			{
				throw new Exception('No data array or ID passed!');
			}
		}
		catch (Exception $e)
		{
			$this->error('Record not found, Error: %s', array($e->getMessage()));
		}
		
		return $this;
	}
	
	// Destruct
	public function __destruct()
	{
		// Auto save
		if ($this->autoSave)
		{
			$this->save();
		}
	}
	
	// Set
	public function set($key, $value)
	{
		try
		{
			if (!array_key_exists($key, $this->databaseFields) && !in_array($key, $this->additionalFields) && !array_key_exists($key, $this->databaseFieldsFlipped))
			{
				throw new Exception('Invalid key being set');
			}
			
			if (array_key_exists($key, $this->databaseFieldsFlipped))
			{
				$key = $this->databaseFieldsFlipped[$key];
			}
			
			$this->data[$key] = $value;
		}
		catch (Exception $e)
		{
			$this->debug('Set Failed: Key: %s, Value: %s, Error: %s', array($key, $value, $e->getMessage()));
		}
		
		return $this;
	}
	
	// Get
	public function get($key = '')
	{
		return (!empty($key) && isset($this->data[$key]) ? $this->data[$key] : (empty($key) ? $this->data : ''));
	}
	
	// Add
	public function add($key, $value)
	{
		try
		{
			if (!array_key_exists($key, $this->databaseFields) && !in_array($key, $this->additionalFields) && !array_key_exists($key, $this->databaseFieldsFlipped))
			{
				throw new Exception('Invalid data being set');
			}
			
			if (array_key_exists($key, $this->databaseFieldsFlipped))
			{
				$key = $this->databaseFieldsFlipped[$key];
			}
			
			$this->data[$key][] = $value;
		}
		catch (Exception $e)
		{
			$this->debug('Add Failed: Key: %s, Value: %s, Error: %s', array($key, $value, $e->getMessage()));
		}
		
		return $this;
	}
	
	// Remove
	public function remove($key, $object)
	{
		try
		{
			if (!array_key_exists($key, $this->databaseFields) && !in_array($key, $this->additionalFields) && !array_key_exists($key, $this->databaseFieldsFlipped))
			{
				throw new Exception('Invalid data being set');
			}
			
			if (array_key_exists($key, $this->databaseFieldsFlipped))
			{
				$key = $this->databaseFieldsFlipped[$key];
			}
			
			foreach ((array)$this->data[$key] AS $i => $data)
			{
				if ($data->get('id') != $object->get('id'))
				{
					$newDataArray[] = $data;
				}
			}
			
			$this->data[$key] = (array)$newDataArray;
		}
		catch (Exception $e)
		{
			$this->debug('Remove Failed: Key: %s, Object: %s, Error: %s', array($key, $object, $e->getMessage()));
		}
		
		return $this;
	}
	
	// Save
	public function save()
	{
		try
		{
			// Error checking
			if (!$this->isTableDefined())
			{
				throw new Exception('No Table defined for this class');
			}
			
			// Variables
			$fieldData = array();
			$fieldsToUpdate = $this->databaseFields;
			
			// Remove unwanted fields for update query
			foreach ($this->databaseFields AS $name => $fieldName)
			{
				if (in_array($name, $this->databaseFieldsToIgnore))
				{
					unset($fieldsToUpdate[$name]);
				}
			}
			
			// Build insert key and value arrays
			foreach ($this->databaseFields AS $name => $fieldName)
			{
				if ($this->get($name) != '')
				{
					$insertKeys[] = $this->DB->sanitize($fieldName);
					$insertValues[] = $this->DB->sanitize($this->get($name));
				}
			}
			
			// Build update field array using filtered data
			foreach ($fieldsToUpdate AS $name => $fieldName)
			{
				$updateData[] = sprintf("`%s` = '%s'", $this->DB->sanitize($fieldName), $this->DB->sanitize($this->get($name)));
			}
			// Force ID to update so ID is returned on DUPLICATE UPDATE - No ID was returning when A) Nothing is inserted (already exists) or B) Nothing is updated (data has not changed)
			$updateData[] = sprintf("`%s` = LAST_INSERT_ID(%s)", $this->DB->sanitize($this->databaseFields['id']), $this->DB->sanitize($this->databaseFields['id']));
			
			// Insert & Update query all-in-one
			$query = sprintf("INSERT INTO `%s` (`%s`) VALUES ('%s') ON DUPLICATE KEY UPDATE %s",
				$this->DB->sanitize($this->databaseTable),
				implode("`, `", $insertKeys),
				implode("', '", $insertValues),
				implode(', ', $updateData)
			);

			if (!$this->DB->query($query))
			{
				// Query failed
				throw new Exception($this->DB->error());
			}
			
			// Database query was successful - set ID if ID was not set
			if (!$this->get('id'))
			{
				$this->set('id', $this->DB->insert_id());
			}
			
			// Success
			return true;
		}
		catch (Exception $e)
		{
			$this->debug('Database Save Failed: ID: %s, Error: %s', array($this->get('id'), $e->getMessage()));
		}
	
		// Fail
		return false;
	}
	
	// Load
	public function load($field = 'id')
	{
		try
		{
			// Error checking
			if (!$this->isTableDefined())
			{
				throw new Exception('No Table defined for this class');
			}
			if (!$this->get($field))
			{
				throw new Exception(sprintf('Operation field not set: %s', strtoupper($field)));
			}
			// Build query
			if (is_array($this->get($field)))
			{
				// Multiple values
				foreach ($this->get($field) AS $fieldValue)
				{
					$fieldData[] = sprintf("`%s`='%s'", $this->DB->sanitize($this->databaseFields[$field]), $this->DB->sanitize($fieldValue));
				}
				
				$query = sprintf($this->loadQueryTemplateMultiple,
					$this->DB->sanitize($this->databaseTable),
					implode(' OR ', $fieldData)
				);
			}
			else
			{
				// Single value
				$query = sprintf($this->loadQueryTemplateSingle,
					$this->DB->sanitize($this->databaseTable),
					$this->DB->sanitize($this->databaseFields[$field]),
					$this->DB->sanitize($this->get($field))
				);
			}

			// Did we find a row in the database?
			if (!$queryData = $this->DB->query($query)->fetch()->get())
			{
				throw new Exception(($this->DB->error() ? $this->DB->error() : 'Row not found'));
			}

			// Loop returned rows -> Set new data
			foreach ($queryData AS $key => $value)
			{

				$this->set($this->key($key), (string)$value);
			}
			
			// Success
			return true;
		}
		catch (Exception $e)
		{
			// Unset ID -> Error
			$this->set('id', 0)->debug('Database Load Failed: ID: %s, Error: %s', array($this->get('id'), $e->getMessage()));
		}
	
		// Fail
		return false;
	}
	
	// Destroy
	public function destroy($field = 'id')
	{
		try
		{
			// Error checking
			if (!$this->isTableDefined())
			{
				throw new Exception('No Table defined for this class');
			}
			if (!$this->get($field))
			{
				throw new Exception(sprintf('Operation field not set: %s', strtoupper($field)));
			}
			
			// Query row data
			$query = sprintf("DELETE FROM `%s` WHERE `%s`='%s'",
				$this->DB->sanitize($this->databaseTable),
				$this->DB->sanitize($this->databaseFields[$field]),
				$this->DB->sanitize($this->get($field))
			);
			
			// Did we find a row in the database?
			if (!$queryData = $this->DB->query($query)->fetch()->get())
			{
				throw new Exception('Failed to delete');
			}
			
			// Success
			return true;
		}
		catch (Exception $e)
		{
			$this->debug('Database Destroy Failed: ID: %s, Error: %s', array($this->get('id'), $e->getMessage()));
		}
	
		// Fail
		return false;
	}
	
	// Key
	public function key($key)
	{
		if (array_key_exists($key, $this->databaseFieldsFlipped))
		{
			return $this->databaseFieldsFlipped[$key];
		}
		
		return $key;
	}
	
	// isValid
	public function isValid()
	{
		try
		{
			foreach ($this->databaseFieldsRequired AS $field)
			{
				if (!$this->get($field))
				{
					throw new Exception(_('Required database field is empty'));
				}
			}
			
			if ($this->get('id') || $this->get('name'))
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			$this->debug('isValid Failed: Error: %s', array($e->getMessage()));
		}
		
		return false;
	}
	
	public function getManager()
	{
		if (!is_object($this->Manager))
		{
			$managerClass = get_class($this) . 'Manager';
			$this->Manager = new $managerClass();
		}
		
		return $this->Manager;
	}
	
	// isTableDefined 
	private function isTableDefined()
	{
		return (!empty($this->databaseTable) ? true : false);
	}
	
	// Name is returned if class is printed
	public function __toString()
	{
		return ($this->get('name') ? $this->get('name') : sprintf('%s #%s', get_class($this), $this->get('id')));
	}
}
