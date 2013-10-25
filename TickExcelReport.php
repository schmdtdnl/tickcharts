<?php

class TickExcelReport
{
	private $report_data;
	private $project_id;
	private $phpexcel;
	
	public function __construct($project_id)
	{
		$this->project_id = $project_id;
		
		require_once ('Classes/PHPExcel/IOFactory.php');
		$this->phpexcel = PHPExcel_IOFactory::createReader('Excel2007');
	}
	
	private function set_document_properties()
	{
		
	}
	
	public function get_report()
	{
		
	}
	
	private function prepare_report_data($project_id)
	{
	
	}
}