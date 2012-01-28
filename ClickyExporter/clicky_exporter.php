<?php
namespace ClickyExporter;

//Autoload using name spaces corresponding to folder names
//All the classes needs to have the same names as their files(except the .php of course) in order for this to work
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 * This is the main file that you run from the command line like this:
 * It will use the file clicky_exporter_config.php to set the default values for the program.
 * Feel free to change the parameters in clicky_exporter_config.php.
 *
 * You can also override the config file params by setting command line parameters.
 * the command line paramters can be supplied in both short and long format.
 *
 * These are the valid parameters to use:
 * --runtimeFolder -r			This is where the output files go. Example: Absolute path "C:/data" or Relative path "data"
 * --clickySiteId -i			This is the site id you can find in the Clicky settings
 * --clickySiteKey -k			This is the site key you can find in the Clicky settings
 * --type -t							This is the type of exporter to use. Example visitor (this is the only exporter that is available for now.)
 * --startDateFormat -s		The date for when to start gathering data. Example: 2012-01-01
 * --endDateFormat -e			The date for when to stop gathering data. Example: 2012-01-01
 * --batchSize -b					The number of records to get in one batch fetch.
 * --folders	-f					Set this to true if you want a folder with a unique name to be created in the runtime folder every time you run the program. If set to false the downloaded files goes striaght in the runtime folder.
 * --timeout	-o					This is the curl timeout value in seconds. You set it to make the download restart if the time limit is passed.
 * Examples:
 *
 * This command gets all the visitors for 2012-01-01
 * c:\php\php.exe php clicky_exporter.php -s 2012-01-01 --endDateFormat 2012-01-01 --type visitor
 */


/**
 * This is the expected number of iterations it will take to get all the data from Clicky.
 * @var integer
 */
$nrIterations = 0;


/**
 * The exporter to be used for getting data from Clicky. It needs to be a class implementing IClickyExporter.
 * Which one to use is decided by the --type parameter.
 * @var IClickyExporter
 */
$theExporter = null;

//Change directory to the directory where this file resides, we do this so the autoloading using the namespaces works correctly
chdir(dirname(__FILE__)."/../");

//Decide what exporter to load
$exporterClass = 'ClickyExporter\Exporter\Clicky'.ucfirst(strtolower(ClickyExporterConfig::singleton()->getParam('type'))).'Exporter';
$theExporter = new  $exporterClass;

//Find out how much data we are getting
$theExporter->fetchNrRecordsToGet();

//Calculate the number of batches we have to run
$nrIterations = $theExporter->getTotalNrBatches();

//Create the runtime folder if it doesn't already exists
if(!is_dir(ClickyExporterConfig::singleton()->getParam('runtimeFolder')))
{
  mkdir(ClickyExporterConfig::singleton()->getParam('runtimeFolder'), 0777);
}


$tempFolder = ClickyExporterConfig::singleton()->getParam('runtimeFolder');

//Add a new folder for every time the program is started
$test = ClickyExporterConfig::singleton()->getParam('folders');
if(ClickyExporterConfig::singleton()->getParam('folders') !== 'false')
{
	$tempFolder .= "/".uniqid();
}

//Make sure the folder is writable
mkdir($tempFolder, 0777);

echo("We are here: ".getcwd()."\n");
echo("Output files goes in: ".$tempFolder."\n");
echo("Start downloading...\n");

for($i=1;($i <= $maxIterations || $maxIterations == 0) && $i <= $nrIterations ;$i++)
{
  $outputFileName = $tempFolder. "/".ClickyExporterConfig::singleton()->getParam('type')."s_".$i.".xml";
  $theExporter->fetchDataBatch($i, $outputFileName);
  echo "\rFinished file ". $i." of ".$nrIterations;
  /** Bail out if no data came back. We are checking for four lines or less in the returned result means that only headers were available **/
  if(count(file($outputFileName)) <= 4)
  {
    echo "\nNot enough data bailing out!\n" ;
    unlink($outputFileName);
    break;
  }
}
echo("\nFinished downloading...\n");



