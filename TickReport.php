<?php

/**
 * Tickspot API wrapper class
 *
 * For usage instructions please read the comments and the 
 * tickspot api documentation(http://www.tickspot.com/api).
 *
 * All execute methods will return the response string. 
 * For example you can process this string with SimpleXML.
 *
 * Author: Daniel Schmidt
 * Version: 1.0
 */
class TickReport
{
	private $company;
	private $email;
	private $password;
	private $base_url;
	private $url;
	/**
	 *Set basic information for tick API
	 *
	 * @param string $company
	 * @param string $email
	 * @param string $password
	 */
	public function __construct($company, $email, $password)
	{
		$this->company = $company;
		$this->email = $email;
		$this->password = $password;
		$this->set_url();
	}
	/**
	 * Private function for setting the base url for api calls
	 */
	private function set_url()
	{
		$this->base_url = 'http://' . $this->company . '.tickspot.com/api/';
	}
	/**
	 * If you want to create a custom api request, first set the type of api
	 * which you want to use.
	 * You can choose from: clients, projects, tasks, clients_projects_tasks, 
	 * entries, recent_tasks, users, create_entry, update_entry
	 *
	 * @param string $api
	 */
	public function set_api($api)
	{
			$this->url = $this->base_url . $api . '?email=' . $this->email . '&password=' . $this->password;
	}
	/**
	 * For custom api requests you can set the parameters here
	 *
	 * @param array $params for possible keys please read the tickspot docs 
	 */
	public function set_params($params)
	{
		foreach ($params as $key => $value)
		{
			$this->url .= '&' . $key . '=' . $value ;
		}
	}
	/**
	 * Get informations of a project
	 *
	 * @param int $project_id
	 */
	public function get_project($project_id)
	{
		$this->set_api('projects');
		$this->set_params(array(
			'project_id' => $project_id,
		));
		return $this->execute();
	}
	/**
	 * Get all entries from a specific project.
	 * Date format: YYYY-MM-DD
	 *
	 * @param $project_id
	 * @params string $start_date optional, if not given, the current months first day will be used
	 * @params string $end_date optional, if not given, the current months last day will be used
	 */
	public function get_entries_for_project($project_id, $start_date = false, $end_date = false)
	{
		if (!$start_date || !$end_date)
		{
			$start_date = date('Y-m-d', mktime(0, 0, 0,  date("n"), 1, date("Y")));
			$end_date = date('Y-m-t', mktime(0, 0, 0, date("n"), 1, date("Y")));
		}
		$this->set_api('entries');
		$this->set_params(array(
			'project_id' => $project_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
		));
		return $this->execute();
	}
	/**
	 * Get all entries from a specific project for a specific user.
	 * Date format: YYYY-MM-DD
	 *
	 * @param $project_id
	 * @param $user_id
	 * @params string $start_date optional, if not given, the current months first day will be used
	 * @params string $end_date optional, if not given, the current months last day will be used
	 */
	public function get_entries_for_project_user($project_id, $user_id, $start_date = false, $end_date = false)
	{
		if (!$start_date || !$end_date)
		{
			$start_date = date('Y-m-d', mktime(0, 0, 0,  date("n"), 1, date("Y")));
			$end_date = date('Y-m-t', mktime(0, 0, 0, date("n"), 1, date("Y")));
		}
		$this->set_api('entries');
		$this->set_params(array(
			'project_id' => $project_id,
			'user_id' => $user_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
		));
		return $this->execute();
	}
	/**
	 * Executes an api request
	 *
	 * @return string response of the tickspot api
	 */
	public function execute()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
}

?>