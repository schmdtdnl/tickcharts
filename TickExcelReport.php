<?php

class TickExcelReport
{
	private $report_data;
	private $project_id;
	private $phpexcel;
	private $report;
	
	public function __construct(TickReport $report)
	{
		$this->report = $report;
		
		require_once ('Classes/PHPExcel/IOFactory.php');
		$this->phpexcel = PHPExcel_IOFactory::createReader('Excel2007');
	}
	
	private function set_document_properties()
	{
		//set base information title, author, etc.
	}
	
	public function get_report($project_id)
	{
		$this->project_id = $project_id;
		$this->get_tasks();
	}
	
	private function prepare_report_data()
	{
		
	}
	
	/*
	 * Get tasks, names(percents per task), time
	 */
	private function get_tasks()
	{
		require_once('project_configs/982715_config.php');
		$project = $this->report->get_project(982715);
		try 
		{ 
			$project_xml = new SimpleXMLElement($project);	
		} 
		catch (Exception $e) 
		{
			echo 'bad xml';
			return false;
		}
		// it's not working :)
		foreach ($project_xml->project->tasks as $one)
		{
			if (array_key_exists($one['id'],$project_config['tasks']))
			{
				$this->report_data[$one['id']] = array
				(
					'id' 			=> $one['id'],
					'name' 			=> $one['name'],
					'budget'		=> $one['budget'],
					'hours'			=> $one['sum_hours'],
					'plus_hours' 	=> ($one['sum_hours'] > $one['budget']) ? ($one['sum_hours'] - $one['budget']) : (0),
					'fee'			=> $project_config['tasks'][$one['id']]['price'],
					'price_wo_decrease' => $this->calculate_price($one['sum_hours'], $project_config['tasks'][$one['id']]['price'], $one['budget'], false),
					'price_w_decrease'	=> $this->calculate_price($one['sum_hours'], $project_config['tasks'][$one['id']]['price'], $one['budget'], true),
				);
			}
		}
	}
	
	/**
	 * This function has do be developed for each project separately(by the project contract)
	 */
	private function calculate_price($hours, $fee, $budget, $decrease = false)
	{
		if (!$decrease)
		{
			return ($hours * $fee);
		}
		
		$plus_hours = ($hours > $budget) ? ($hours - $budget) : (0);
		
		if ($plus_hours == 0)
		{
			return $hours * $fee;
		}
		
		// 5% from the plus time is free
		$decreased_plus_time = $plus_hours * 0.95;
		
		$all_hours = $budget + $decreased_plus_time;
		
		return $all_hours * $fee;
	}
}