<?php
class Kbridge
{
	public static $inited = false;
	
	public static function init()
	{
		if (self::$inited == true) throw new Exception('Kbridge is already initialized!');
		
		self::$inited = true;
		
		define('EXT', '.php');
		define('SYSPATH', Yii::app()->basePath.'/kohana/');
		
		// Define the front controller name and docroot
		define('DOCROOT', getcwd().DIRECTORY_SEPARATOR);
		define('KOHANA',  basename(__FILE__));

		// If the front controller is a symlink, change to the real docroot
		is_link(KOHANA) and chdir(dirname(realpath(__FILE__)));

		// Define application and system paths
		define('APPPATH', str_replace('\\', '/', realpath('../')).'/');
		define('MODPATH', str_replace('\\', '/', realpath('modules')).'/');
		
		require SYSPATH.'core/utf8.php';
	}
	
	public static function autoload($className)
	{
		$className = (string)$className;
		
		$app_model_file = Yii::app()->basePath.'/models/'.$className.'.php';
		$k_core_file = SYSPATH.'core/'.$className.'.php';
		$k_helper_file = SYSPATH.'helpers/'.$className.'.php';
		$k_library_file = SYSPATH.'libraries/'.$className.'.php';
		
		if (is_file($app_model_file))
		{
			require $app_model_file;
		}
		elseif (is_file($k_core_file))
		{
			require $k_core_file;
		}
		elseif (is_file($k_helper_file))
		{
			require $k_helper_file;
			eval("class $className extends {$className}_Core {}");
		}
		elseif (is_file($k_library_file))
		{
			require $k_library_file;
			eval("class $className extends {$className}_Core {}");
		}
		else
		{
			YiiBase::autoload($className);
		}
	}
}

spl_autoload_unregister(array('YiiBase', 'autoload'));
spl_autoload_register(array('Kbridge', 'autoload'));