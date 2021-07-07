<?php
session_start();
$core = $_SESSION["CORE"];
require_once($core."Calendar.php");
require_once($core."Complexe.php");
$complexes = $complexe->find("",array("conditions"=>array("status="=>1),"order"=>"name"),"v_complexe");

require_once($core."Contrat.php");
?>
<div id="page" class="dashbord">
	<div class="head">
		<div class="title">
			<div class="name"><i class="fas fa-chart-line"></i> Dashbord</div>
		</div>
	</div>

	<div class="body">
		<div class="row">
			<div class="col_12">
				<div id="mycalendar">
					<div class="mycalendar-container">
						<div class="mycalendar-header">
							<div class="title"><i class="far fa-calendar-alt"></i> Calendar</div>
							<div class="tabs">
								<ul>
									<li><a class="active" data-style="1" href="#tab1">Mois</a></li>
									<li><a class="" data-style="2">Société</a></li>
									<li><a href="" data-style="3">Appartement</a></li>
								</ul>
							</div>
						</div>
						
						<div class="mycalendar-body pb-20">
								<?= $calendar->Get(["style"=>1,"counter"=>0, "id_complexe"=>"21", "UID"=>"fc8267c6"]) ?>							
						</div>
						
					</div>
				</div>
			</div>
		</div>


		<div class="shadow rounded border mx-2 blabla">
			container
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		var data = {
			'controler'		:	'Calendar',
			'function'		:	'Data_Of_By_Societe',
			'params'		:	{
				'month'			:	07,
				'year'			:	2021,
				'id_complexe'	:	17
			}
		};
		$.ajax({
			type		: 	"POST",
			url			: 	"pages/default/ajax/ajax.php",
			data		:	data,
			dataType	: 	"json",
		}).done(function(response){
			console.log(response);
			$(".blabla").html(response.msg);
			
		}).fail(function(xhr) {
			alert("Error");
			console.log(xhr.responseText);
		});
	});
</script>