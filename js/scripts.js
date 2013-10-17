google.load("visualization", "1", {packages:["corechart"]});
var d = new Date();

function drawChart(data) 
{
	var vis_data = google.visualization.arrayToDataTable(data[1]);
	
	var max_days = parseInt(daysInMonth());
	
	var options = {
		title: data[0],
		hAxis: {
			viewWindow: {
				max: max_days,
				viewWindowMode: 'explicit'
			}
		},
		vAxis: {
			viewWindow: {
				max: 100
			}
		}
	};

	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(vis_data, options);
	$('#loading').hide();
}
	  
function daysInMonth() {
    return new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
}

function getAvailableProjects()
{
	$.ajax({
		type: "GET",
		url: "source.php?f=get_projects",
		dataType: "json",
		success: function(res)
		{
			for (var i = 0 ; i < res.length ; i++)
			{
				$('#project').append('<option value="'+res[i]+'">'+res[i]+'</option>');
			}
			$('#nav').show();
		}
	});
}
	
function getChart(project_id)
{
	$.ajax({
		type: "GET",
		url: "source.php?f=get_project&id=" + project_id,
		dataType: "json",
		success: function(res)
		{
			drawChart(res);
		},
		error: function (o, err)
		{
			console.log(err);
		}
	});
}

$(function(){
	getAvailableProjects();
	
	$('#project').on('change',function(){
		$('#loading').show();
		getChart($(this).val());
	});
	
	$('#refresh').on('click',function(){
		$('#loading').show();
		getChart($('#project').val());
	});
});