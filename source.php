<?php
require_once('TickReport.php');
require_once('TickChart.php');

$report = new TickReport('*****', '*****', '*****');
$charts = new TickChartMonthlyTime($report);

switch ($_GET['f'])
{
	case 'get_project':
		$data = $charts->get_chart_data($_GET['id']);
		echo json_encode($data);
		break;
	case 'get_projects':
	default:
		$projects = $charts->get_projects();
		echo json_encode($projects);
		break;
}
?>