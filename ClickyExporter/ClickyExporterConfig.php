<?php
namespace ClickyExporter;
//This is the config file keeping the config array $configFileParamArray
//It is only there so it is easy for the user to set persistent application settings
include('clicky_exporter_config.php');

/**
 * This class is a singleton implementation of a prameter storage.
 * It merges the config file array with the options set on the command line.
 * It comes out with a final array holding the config parameters.
 *
 * To access this class externally you should always use this syntax ClickyExporterConfig::singleton(). Doing that
 * ensures that we only keep one instance of this class and we can globally access it from anywhere in the code
 * where we need application wide settings. This way we don't have to pass around a config array wherever we need it.
 *
 * @author John Eskilsson
 *
 */
class ClickyExporterConfig
{
	/**
	 * Keeping the singleton instance
	 * @var ClickyExporterConfig
	 */
	private static $instance;

	/**
	 * An array defining the long and short name options of the command line parameters.
	 * Every short/long parameter pair mapping is kept in an array inside the full array.
	 *
	 * The first item of the short/long array is the long key using the : to define if the parameter is optional or not.
	 * The second item of the short/long array is the short key using the : to define if the parameter is optional or not.
	 *
	 * If a parameter is added to the config file it needs get an entry in this array.
	 *
	 * The long key must map to the entry in the config file. It should be camel cased.
	 *
	 * @var array
	 */
	private $_opts  = array(
	array("runtimeFolder:","r:"),
	array("clickySiteId:","i:"),
	array("clickySiteKey:","k:"),
	array("type:", "t:"),
	array("startDateFormat:", "s:"),
	array("endDateFormat:", "e:"),
	array("batchSize:", "b:"),
	array("folders:", "f:"),
	array("timeout:", "o:"));

	/**
	 * This is the name value config array that have been built up from the config file and the command line
	 * parameters.
	 *
	 * This is the storage for all the application wide config parameters.
	 *
	 * @var array
	 */
	private $_finalConfigArray = array();

	/**
	 * This is the private constructor that creates the one and only instance of the config class.
	 */
	private function __construct()
	{
		//We make sure we can see the configuration array included from clicky_exporter_config.php';
		global $configFileParamArray;
		//Make sure to override with the parameters given on the command line
		$this->setCommandLineParameters($this->_opts, $configFileParamArray);
	}

	/**
	 * Controls the singleton behaviour
	 *
	 * @return ClickyExporterConfig
	 */
	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}


	/**
	 * Return the full config array containing data from the config file and the
	 * command line parameters.
	 */
	public function getConfigArray()
	{
		return $this->_finalConfigArray;
	}

	/**
	 * Take care of the command line parameters that are supplied and make them override the
	 * data in the config file.
	 *
	 * @param array $opts This is the array specifying what command line parameters are available.
	 * @param array $configFileParamArray This is a name value config file array that will be overridden by values in $opts
	 *
	 * @return array This is the full array after merging config file and command line parameters. It is also set in a private variable.
	 */
	private function setCommandLineParameters($opts=array(), $configFileParamArray=array())
	{
		/**
		 * Keeps the string needed to define the short parameters
		 * @var string
		 */
		$shortOpts = "";

		/**
		* Keeps the array needed to define the long parameters
		* @var array
		*/
		$longOpts = array();

		//Prepare for running the values through getopts
		foreach ($opts AS $opt)
		{
			$shortOpts .= $opt[1];
			$longOpts[] = $opt[0];
		}

		//Merge the long and short values
		$commandLineOptionsArray = getopt($shortOpts, $longOpts);

		//Fill up the final array with config file data
		foreach ($configFileParamArray AS $paramKey => $paramValue)
		{
			$this->_finalConfigArray[$paramKey] = $paramValue;
		}

		//Override with the values from command line
		foreach($commandLineOptionsArray AS $commandLineOptKey => $commandLineOptValue)
		{
			//Match up the supplied command line options defined opts
			foreach ($opts as $opt)
			{
				//The short keys cannot be used straight away to override the config file values
				//if it is a short key we need to look up the corresponding long key
				if(strlen($commandLineOptKey) == 1 )
				{
					//Strip the character : out so we can hit the index in the array
					$opt[0] = str_replace(':','', $opt[0]);
					$opt[1] = str_replace(':','', $opt[1]);

					//If match we can now override the value
					if($opt[1] == $commandLineOptKey)
					{
						$this->_finalConfigArray[$opt[0]] = $commandLineOptValue;
					}
				}
				//This is the case for long style command line parameters
				else
				{
					//Strip the character : out so we can hit the index in the array
					$commandLineOptKey = str_replace(':','', $commandLineOptKey);
					$this->_finalConfigArray[$commandLineOptKey] = $commandLineOptValue;
				}
			}
		}

		echo("****Start application Parameters*********************************\n");
		//Print the values that are actually being used for reassurance for the user
		foreach ($this->_finalConfigArray as $finalArrayOptKey => $finalArrayOptValue)
		{
			echo $finalArrayOptKey. ": ".$finalArrayOptValue. "\n";
		}
		echo("*****************************************************************\n");
		return $this->_finalConfigArray;
	}

	/**
	 * Get a config parameter based on the long key value.
	 *
	 * @param string $key The key matching the long key. For instance 'type'
	 * @return mixed The value of the parameter in the storage
	 */
	public function getParam($name)
	{
		if(array_key_exists($name, $this->_finalConfigArray) == false)
		{
			echo("There is no parameter called :". $name. "\n");
		}
		else
		{
			return $this->_finalConfigArray[$name];
		}
	}

	/**
	 * Set a config parameter based on the long key value.
	 *
	 * @param string $name The key matching the long key. For instance 'type'
	 * @param mixed $value The value you want to set
	 */
	public function setParam($name, $value)
	{
		if(array_key_exists($name, $this->_finalConfigArray) == false)
		{
			echo("Cannot set the value ". $value." there is no parameter called :". $name. "\n");
		}
		else
		{
			$this->_finalConfigArray[$name] = $value;
		}
	}
	/**
	 * Dont allow cloning on a singleton class
	 */
	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	/**
	* Dont allow unserializing on a singleton class
	*/
	public function __wakeup()
	{
		trigger_error('Unserializing is not allowed.', E_USER_ERROR);
	}
}
