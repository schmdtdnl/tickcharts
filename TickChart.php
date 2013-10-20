<?php

class TickChart
{
	protected $project_configs_dir = './project_configs';
	protected $report;
	protected $projects;
	protected $project_id;
	protected $project;
	protected $entries;
	
	public function __construct(TickReport $report)
	{
		$this->report = $report;
		$this->fetch_projects();
	}
	
	private function fetch_projects()
	{
		if ($handle = opendir($this->project_configs_dir)) 
		{
			while (false !== ($entry = readdir($handle))) 
			{
				if (is_file($this->project_configs_dir . '/' . $entry))
				{
					$this->projects[] = preg_replace('/_config\.php/', '', $entry);
				}
			}
			closedir($handle);
		}
	}
	
	public function get_projects()
	{
		return $this->projects;
	}
	
	
}

class TickChartMonthlyTime extends TickChart
{
	public function __construct(TickReport $report)
	{
		parent::__construct($report);
	}

	public function get_chart_data($project_id)
	{
		if (!in_array($project_id, $this->projects))
		{
			return false;
		}
		require_once($this->project_configs_dir . '/' . $project_id . '_config.php');
		
		$this->project = $this->report->get_project($project_id);
		$this->entries = $this->report->get_entries_for_project($project_id, $project_config['time']['start'], $project_config['time']['end']);
		
		try 
		{ 
			$project_xml = new SimpleXMLElement($this->project);
			$entries_xml = new SimpleXMLElement($this->entries);		
		} 
		catch (Exception $e) 
		{
			echo 'bad xml';
			return false;
		}
		
		/*
		$sum_money = 0;
		foreach ($project_xml->project->tasks->task as $task)
		{
			$sum_money += $task_money[(string)$task->id]['price'] * $task->budget;
		}
		*/
		
		$sum_budget = $project_xml->project->budget;

		$daily_data = array(
			array
			(
				'nap', 
				'idő',
				'opt',
			),
		);

		$daily_data = $this->get_opt($daily_data, $project_config);

		$daily_hours = array();
		$daily_money = array();

		foreach ($entries_xml as $entry)
		{	
			$time = strtotime( $entry->date );
			$day = date('j', $time);
			
			//$entry_money = $entry->hours * $task_money[(string)$entry->task_id]['price'];
			
			if (array_key_exists($day, $daily_hours) && array_key_exists('time', $daily_hours[$day]))
			{
				$daily_hours[$day]['time'] = $daily_hours[$day]['time'] + $entry->hours;
			}
			else
			{
				$daily_hours[$day]['time'] = $entry->hours;
			}
			/*
			if (array_key_exists($day, $daily_hours) && array_key_exists('money', $daily_hours[$day]))
			{
				$daily_hours[$day]['money'] = $daily_hours[$day]['money'] + $entry_money;
			}
			else
			{
				$daily_hours[$day]['money'] = $entry_money;
			}
			*/
			$daily_hours[$day]['day'] = $day;
		}
		usort($daily_hours, array('TickChartMonthlyTime','cmp'));

		$sum_time = 0; 
		$sum_daily_money = 0;
		
		foreach ($daily_hours as $day => $one)
		{
			// idő százalék

			$sum_time += $one['time'];
			$time_percent = ($sum_time  / $sum_budget) * 100;
			
			// pénz százalék
			/*
			$sum_daily_money += $one['money'];
			$money_percent = ($sum_daily_money  / $sum_money) * 100;
			*/
			
			$daily_data[$one['day']] = array
			(
				$one['day'],
				$time_percent,
				$daily_data[$one['day']][2],
			);
		}
		
		$prev_time_percent = 0;
		for ($i = 0 ; $i < count($daily_data) ; $i++)
		{
			if ($daily_data[$i][1] == 0)
			{
				$daily_data[$i][1] = $prev_time_percent;
			}
			$prev_time_percent = $daily_data[$i][1];
		}
		
		$return_data = array
		(
			$project_config['meta']['name'],
			$daily_data,
		);
		
		return $return_data;
	}
	
	private function get_opt($daily_data, $project_config)
	{
		$max_days = date("t", mktime(0,0,0,$project_config['time']['month']));
		for ($i = 1 ; $i <= $max_days ; $i++)
		{
			$percent = ($i/$max_days) * 100;
			/*
			if (date('N', strtotime($project_config['time']['month'].'-'.$project_config['time']['month'].'-'.$i)) >= 6 && $i != 1)
			{
				$daily_data[$i] = $daily_data[$i-1];
			}
			else
			{
			*/
				$daily_data[$i] = array((string)$i,0,$percent);
			/*	
			}
			*/
		}
		return $daily_data;
	}
	
	private static function cmp($a, $b)
	{
		if ($a['day'] == $b['day']) {
			return 0;
		}
		return ($a['day'] < $b['day']) ? -1 : 1;
	}
}