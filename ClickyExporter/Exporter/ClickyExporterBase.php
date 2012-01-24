<?php
namespace ClickyExporter\Exporter;
use ClickyExporter\ClickyExporterConfig;
/**
 * This is the base class for the different data exporters.
 * It mostly take care of magic getters and setters. It keeps the common data variables
 * for the specialised exporters.
 *
 * This class is abstract since it is a base class and can therefore not be instantiated.
 * In order to create an exporter that can be instantiated you need extend this class.
 * @see ClickyVisitorExporter
 *
 * @author John Eskilsson
 *
 */
abstract Class ClickyExporterBase
{
  /**
   * This is the start date on this format '2012-01-30'
   * @var string
   */
  protected $startDateFormat;
  /**
   * This is the start date on this format '2012-01-30'
   * @var string
   */
  protected $endDateFormat;

  /**
   * The size of the batches we get, the maximum Clicky supports is 5000
   * @var integer
   */
  protected $batchSize;


  /**
   * The number of record to get from Clicky.
   * This parameter shall be populated for the method fetchNrRecordsToGet in the IClickyExporter interface.
   * @var unknown_type
   */
  protected $nrRecordsToGet;


  /**
   * This is the type of importer. An example is visitor.
   */
  protected $type;

  public function __construct()
  {
    //Set up the class variables
    $this->startDateFormat = ClickyExporterConfig::singleton()->getParam('startDateFormat');
    $this->type = ClickyExporterConfig::singleton()->getParam('type');
    $this->endDateFormat = ClickyExporterConfig::singleton()->getParam('endDateFormat');
    $this->batchSize = ClickyExporterConfig::singleton()->getParam('batchSize');
  }

  public function __set($name, $value)
  {
    $this->$name = $value;
  }

  public function __get($name)
  {
    echo "Getting '$name'\n";
    return $this->$name;

    $trace = debug_backtrace();
    trigger_error(
      'Undefined property via __get(): ' . $name .
      ' in ' . $trace[0]['file'] .
      ' on line ' . $trace[0]['line'],
      E_USER_NOTICE);
    return null;
  }

  /**
   * This function returns how many batches it takes to get the total number of rows. The last batch might
   * of course contain less data than the other files.
   *
   * @return integer The number of batches
   *
   */
  public function getTotalNrBatches()
  {
    $totalNrBatches=0;

    //Make sure that we have set the batch size so it makes sense to fetch some data
    if($this->batchSize>0)
    {
      $totalNrBatches = intval(ceil($this->nrRecordsToGet / $this->batchSize));
    }
    else
    {
      echo "Batch size must be more than 0. It was ". $batchSize."\n";
    }
    return $totalNrBatches;
  }
}
