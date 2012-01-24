<?php
namespace ClickyExporter\Exporter;
use ClickyExporter\ClickyExporterConfig;

/**
 * This class i responsible for downloading the visitor records from Clicky.
 *
 * @author John Eskilsson
 *
 */
class ClickyVisitorExporter Extends ClickyExporterBase implements IClickyExporter
{
	/**
	 * This function fetches the number of records we need to get from clicky.
	 * (non-PHPdoc)
	 * @see ClickyExporter\Exporter.IClickyExporter::fetchNrRecordsToGet()
	 */
  public function fetchNrRecordsToGet()
  {
    $ch = curl_init();
    $clickyUrl = "http://api.getclicky.com/api/stats/4?site_id=". ClickyExporterConfig::singleton()->getParam('clickySiteId') . "&sitekey=". ClickyExporterConfig::singleton()->getParam('clickySiteKey'). "&type=".$this->type."s&date=".$this->startDateFormat.",".$this->endDateFormat."&output=php";
    curl_setopt($ch, CURLOPT_URL, $clickyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0');
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $nrVisitorsToGetJsonResponse = unserialize(curl_exec($ch));

    //Take care of the errors that might be reported by Clicky
    if(array_key_exists('error', $nrVisitorsToGetJsonResponse) != false)
    {
    	//Push the Clicky errors straight out on the command line to the user and stop execution, if we get
    	//an error here there is no point in continuing.
      echo("Clicky Error: ".$nrVisitorsToGetJsonResponse['error']."\n");
      exit();
    }

    $nrRecordsToGet = $nrVisitorsToGetJsonResponse['visitors'][$this->startDateFormat.",".$this->endDateFormat][0]['value'];
    echo("Nr of ".$this->type."s to get: ". $nrRecordsToGet. " in batches of $this->batchSize\n");
    $this->nrRecordsToGet = $nrRecordsToGet;
    return $nrRecordsToGet;
  }

  /**
   * This function gets the data files from Clicky.
   * It does it in batches specified in the config and puts it in
   * files named after the record type. This way two exporters can
   * be run at the same time writing to the same folder if needed.
   *
   * (non-PHPdoc)
   * @see ClickyExporter\Exporter.IClickyExporter::fetchDataBatch()
   */
  public function fetchDataBatch($page, $outputFileName)
  {
    $fh = fopen($outputFileName, 'w') or die("can't open file");

    $clickyUrl = "http://secure.getclicky.com/stats/api4?site_id=".ClickyExporterConfig::singleton()->getParam('clickySiteId')."&date=".$this->startDateFormat.",".$this->endDateFormat."&sitekey=".ClickyExporterConfig::singleton()->getParam('clickySiteKey')."&type=".$this->type."s-list&output=xml&download&limit=".$this->batchSize."&page=".$page;
    /**
     * This varaible will be set to false if the fetch from Clicky fails
     * because of this it will retry getting data.
     * Sometimes the fetches time out but normally we can get the data quickly the next time since the DB
     * caches are warm.
     */
    $fetchSuccess = false;
    while($fetchSuccess == false)
    {
      $ch = curl_init($clickyUrl);
      curl_setopt($ch, CURLOPT_TIMEOUT, 180);
      curl_setopt($ch, CURLOPT_FILE, $fh);
      curl_setopt($ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

      $result = curl_exec($ch);
      //Sometimes getting the data from Clicky fails and we need to try again to not lose data
      if($result == false)
      {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        fclose($fh);
        unset($fh);
        $fetchSuccess = false;
        echo("Failed getting batch : ". $page." retrying...\n");
        continue;
      }
      else
      {
      	//This is to indicate that we don't need to retry downloading the data
        $fetchSuccess = true;
      }
      //Clean up
      curl_close($ch);
      fclose($fh);
      unset($fh);
    }
    //Batch successfully fetched
    return true;
  }
}
?>
