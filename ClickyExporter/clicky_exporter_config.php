<?php
namespace ClickyExporter;
/**
 * This file makes it possible to set the default values for the clicky exporter.
 * E.g It can be useful to set the siteId and siteKey in here so that you don't
 * have to supply it on the command line every time you run the script.
*/

//Control the memory used by the application
ini_set('memory_limit','128M');

//Start editable variables
/**
 * This is where the downloaded XML files goes.
 * No trailing slash needed.
 *
 * You can give an absolute path like "c:/myfolder"
 * You can also give a relative path "myfolder" and it will write the folder in the directory where
 * you are running the application.
 * @var string
 */
$runtimeFolder = "data";

/**
 * This is the site id you find in the preferences of Clicky
 * @var integer
 */
$clickySiteId = "123";

/**
 * This is the site key you find in the preferences of Clicky.
 * @var string
 */
$clickySiteKey = "12345";

/**
 * This is the day to start getting data from
 * @var string
 */
$startDateFormat = "2012-01-13";

/**
 * This is the last day to get data from
 * @var string
 */
$endDateFormat = "2012-01-13";

/**
 *
 * This is the number of visitors to get in one go. The max we can get is 5000
 * @var integer
 */
$batchSize = 1000;

/**
 * This is the type of exporter we want to run. The default one is visitors.
 * This parameter can be used to load plugged in external exporters.
 * If the exporter is called ClickyVisitorExporter the type should be visitor
 * if it is called ClickyActionExporter it should be called action.
 *
 * @var string
 */
$type = "visitor";

/**
 * Set this to true if you want a folder with a unique name to be created in
 * the runtime folder every time you run the program. If set to false the
 * downloaded files goes striaght in the runtime folder.
 * @var boolean
 */
$folders = true;

/**
 * This is the number of batch fetches allowed. Use this if we need to limit the maximum number of visitors to fetch.
 * 0 means no limit
 * @var integer
 */
$maxIterations = 0;

/**
 * This is the timeout for Curl in seconds. When it time outs the download will be restarted for the specific file/batch
 * if you are on a slow flaky connection this should probably be set higher but it is all depending on how big batches 
 * you are running.
 */
$timeout = 180;

//End editable variables

$configFileParamArray = array(
	'runtimeFolder' 	=> $runtimeFolder,
	'clickySiteId' 		=> $clickySiteId,
	'clickySiteKey' 	=> $clickySiteKey,
	'startDateFormat' 	=> $startDateFormat,
	'endDateFormat'		=> $endDateFormat,
	'batchSize'		=> $batchSize,
	'maxIterations'		=> $maxIterations,
	'type'			=> $type,
	'folders'		=> $folders,
	'timeout'		=> $timeout);
?>
