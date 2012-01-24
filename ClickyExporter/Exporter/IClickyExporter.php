<?php
namespace ClickyExporter\Exporter;
/**
 * This interface specifies how a Clicky exporter shall act.
 * It will force the implementing class to find out how many records we are trying to get.
 * This is mostly so that we can progress report in a good way.
 *
 * It also knows how to get on batch/page of datafrom Clicky for the specific record(something like visitors or actions).
 *
 * @author John Eskilsson
 *
 */
interface IClickyExporter
{
	/**
	 * This method tries to get the number of record to get from Clicky.
	 * It shall populate the attribute $nrRecordsToGet in the implementing class
	 */
	public function fetchNrRecordsToGet();

	/**
	 * This method shall be implemented to retrieve one batch of data from Clicky.
	 * Every batch is written to disk as a file.
	 *
	 * @param integer $page The pagination counter
	 * @param string $outputFileName The file that we are writing to
	 *
	 * @return true when the we successfully downloaded the file.
	 */
	public function fetchDataBatch($page, $outputFileName);
}

?>
