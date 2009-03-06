<?php
class Kohana
{
	private static $configuration;
	
	public function init()
	{
		define('EXT', '.php');
		define('SYSPATH', Yii::app()->basePath.'/kohana/');
		define('APPPATH', SYSPATH);
		define('KPATH', basename(dirname(__FILE__)));
		
		Yii::import('application.'.KPATH.'.core.utf8');
	}
	
	public static function autoload($className)
	{
		if ($className[0] == 'K')
		{
			$realClassName = substr($className, 1);
			$k_helper_file = SYSPATH.'helpers/'.$realClassName.'.php';

			if (is_file($k_helper_file))
			{
				require $k_helper_file;
				eval("class $className extends {$realClassName}_Core {}");
			}
			else
			{
				YiiBase::autoload($className);
			}
		}
		else
		{
			YiiBase::autoload($className);
		}
	}
	
	/**
	 * Get a config item or group.
	 *
	 * @param   string   item name
	 * @param   boolean  force a forward slash (/) at the end of the item
	 * @return  mixed
	 * @author  Kohana Team
	 */
	public static function config($key, $slash = FALSE)
	{
		// Get the group name from the key
		$group = explode('.', $key, 2);
		$group = $group[0];
	
		if ( ! isset(self::$configuration[$group]))
		{
			// Load the configuration group
			self::$configuration[$group] = self::config_load($group);
		}
	
		// Get the value of the key string
		$value = self::key_string(self::$configuration, $key);
	
		if ($slash === TRUE AND is_string($value) AND $value !== '')
		{
			// Force the value to end with "/"
			$value = rtrim($value, '/').'/';
		}
	
		return $value;
	}
	
	/**
	 * Load a config file.
	 *
	 * @param   string   config filename, without extension
	 * @param   boolean  is the file required?
	 * @return  array
	 * @author  Kohana Team
	 */
	public static function config_load($name)
	{
		if ($name === 'core')
		{
			// Load the application configuration file
			require APPPATH.'config/config.php';
	
			if ( ! isset($config['site_domain']))
			{
				// Invalid config file
				die('Your Kohana application configuration file is not valid.');
			}
	
			return $config;
		}
	
		if (isset(self::$configuration[$name]))
			return self::$configuration[$name];
	
		// Load matching configs
		$configuration = array();
	
		require_once APPPATH.'config/'.$name.'.php';
	
		if (isset($config) AND is_array($config))
		{
			// Merge in configuration
			$configuration = array_merge($configuration, $config);
		}
	
		return self::$configuration[$name] = $configuration;
	}
	
	/**
	 * Returns the value of a key, defined by a 'dot-noted' string, from an array.
	 *
	 * @param   array   array to search
	 * @param   string  dot-noted string: foo.bar.baz
	 * @return  string  if the key is found
	 * @return  void    if the key is not found
	 * @author  Kohana Team
	 */
	public static function key_string($array, $keys)
	{
		if (empty($array))
			return NULL;
	
		// Prepare for loop
		$keys = explode('.', $keys);
	
		do
		{
			// Get the next key
			$key = array_shift($keys);
	
			if (isset($array[$key]))
			{
				if (is_array($array[$key]) AND ! empty($keys))
				{
					// Dig down to prepare the next loop
					$array = $array[$key];
				}
				else
				{
					// Requested key was found
					return $array[$key];
				}
			}
			else
			{
				// Requested key is not set
				break;
			}
		}
		while ( ! empty($keys));
	
		return NULL;
	}
}

spl_autoload_unregister(array('YiiBase', 'autoload'));
spl_autoload_register(array('Kohana', 'autoload'));