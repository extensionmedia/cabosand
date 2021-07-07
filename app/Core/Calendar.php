<?php
require_once('Helpers/Modal.php');

class Calendar extends Modal{

	private $months = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	
	private $tableName = "Contrat";
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName(strtolower($this->tableName));
		}catch(Exception $e){
			die($e->getMessage());
		}
	}	
	
	/*
	public function days_in_month($month, $year) { 
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
	} 
	*/
	public function days_in_month( $params ) { 
		return $params["month"] == 2 ? ($params["year"] % 4 ? 28 : ($params["year"] % 100 ? 29 : ($params["year"] % 400 ? 28 : 29))) : (($params["month"]-1) % 7 % 2 ? 30 : 31); 
	} 	
	
	public function drawCalendar($month,$year, $args=null){
		
		$calendar = "Error Creating Calendar ...!";
		$counter = $args["options"]["counter"];
		
		if(isset($args["options"]["style"]) && $args["options"]["style"] === "month"){
			
			$counter = isset($args["options"]["counter"])? $args["options"]["counter"]:0;
			$days_in_selected_month = $this->days_in_month(($month + $counter), $year);
			
			$_month = (($month + $counter)>9)? ($month + $counter): "0". ($month + $counter);
			$first_day_date = $year . "-" . $_month . "-01";
			$last_day_date = $year . "-" . $_month . "-" . $days_in_selected_month;
			
			$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
			$calendar.= '<tr class="calendar-row">';
			$row = array();
			for($i=0; $i<$days_in_selected_month; $i++){
				$calendar.= '<td class="calendar-day-head"><span class="days">'.($i+1).'</span></td>';
			}
			$calendar.= '</tr>';
			
			$value="";
			$span = 1;
			//var_dump($args["data"]);
			foreach($args["data"] as $k=>$row){
				$value="";
				$span = 1;
				//var_dump($row);
				foreach($row as $day=>$content){
					
					if($value === $content){
						$span += 1;
						$value = $content;
					}else{
						if($day === 1){
							$span = 1;
							$value = $content;
						}else{
							$extract = explode(";",explode("|",$value)[0]);
								//var_dump($extract);
							if( $extract[0] === "empty"){
								$calendar.= '<td style="border-right:1px solid #ccc; padding:4px 0px" colspan='.$span.'><div class="label label-green" style="padding:3px 5px; background-color:' . $extract[1] . '">vide</div></td>';
							}else{
								$calendar.= '<td style="border-right:1px solid #ccc; padding:4px 0px" colspan='.$span.'><div class="label label-green" style="padding:3px 5px; border-right:5px solid red; border-left:5px solid yellow; background-color:' . $extract[1] . '">' . $extract[0] . '</div></td>';
							}
							
							$span = 1;
							$value = $content;								
						}


					}
				}
				$calendar.= '</tr>';
			}
			//var_dump($row);
			$calendar.= '</table>';
			
		}elseif(isset($args["options"]["style"]) && $args["options"]["style"] === "month_"){
			/* draw table */
			$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

			/* table headings */
			$headings = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
			$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
			$month += $counter;
			/* days and weeks vars now ... */
			$running_day = date('w',mktime(0,0,0,$month,1,$year)); 		// order of first day in the week
			$days_in_month = date('t',mktime(0,0,0,$month,1,$year)); 	// number of days in given month
			$days_in_this_week = 1;
			$day_counter = 0;
			$dates_array = array();

			/* row for week one */
			$calendar.= '<tr class="calendar-row">';

			/* print "blank" days until the first of the current week */
			for($x = 0; $x < $running_day; $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
				$days_in_this_week++;
			endfor;

			/* keep going with days.... */
			for($list_day = 1; $list_day <= $days_in_month; $list_day++):
				$calendar.= '<td class="calendar-day">';
					/* add in the day number */
			
				$day = ($list_day<10)? "0" . $list_day: $list_day;
				$_month = ($month>9)? $month: "0". $month;
				$date = $year . "-" . $_month . "-" . $day;
			
				$calendar.= '<div class="day-number">'.$list_day.'</div>';
				$i	= 	0;
				$j	=	0;
				$complexes = array();
				$hided = "";
				foreach($args["data"] as $k=>$v){

					if( $v["date_debut"] === $date || $v["date_fin"] === $date){
						if(!in_array($v["societe_name"], $complexes)){
							array_push($complexes,$v["societe_name"]);
							if($i<6){
								$calendar.= "<div class='label label-green' style='padding:2px 3px; font-size:10px;background-color:" .  $v["color"] . "'>" .  $v["societe_name"] . "</div>";
							}else{
								$j++;
								$hided .= "<div class='label label-green' style='padding:2px 3px; font-size:10px;background-color:" .  $v["color"] . "'>" .  $v["societe_name"] . "</div>";
							}
							$i++;							
						}
						

					}else{
						/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
					//$calendar.= str_repeat('<p></p>',1);
					}				
				}

				if($j>0){
					$calendar.= "<div class='label label-default calendar_dev' style='padding:2px; font-size:10px;'>" .  $j . " Autres </div>";
					$calendar.= "<div class='hide to_show'>" .  $hided . "</div>";
				}
			
				$calendar.= '</td>';
				if($running_day == 6):
					$calendar.= '</tr>';
					if(($day_counter+1) != $days_in_month):
						$calendar.= '<tr class="calendar-row">';
					endif;
					$running_day = -1;
					$days_in_this_week = 0;
				endif;
				$days_in_this_week++; $running_day++; $day_counter++;
			endfor;

			/* finish the rest of the days in the week */
			if($days_in_this_week < 8):
				for($x = 1; $x <= (8 - $days_in_this_week); $x++):
					$calendar.= '<td class="calendar-day-np"> </td>';
				endfor;
			endif;

			$calendar.= '</tr>';
			$calendar.= '</table>';	
			
		}

		return $calendar;
	}
	
	
	/**********************/
	
	
	public function DrowAppartements(){
		
		$proprieties = $this->find("", ["conditions"=>["is_for_location="=>1]], "propriete");
		$html = '
					<ul>
						<li class="header">
							<div>
								<p>Appartements</p>
							</div>
						</li>
						{{items}}
					</ul>
		';
		$item = "";
		foreach($proprieties as $k=>$v){
			$item .= '	<li>
							<div class="item">
								<div class="text">
									<b>'.$v["code"].'</b>
								</div>
							</div>
						</li>';
		}
		
		return str_replace("{{items}}", $item, $html);
		
	}
	
	
	public function ReadDates($year, $month, $id_propriete){
		
		$request = "select * from propriete_location where ((year(date_debut)=" . $year . " and month(date_debut)=" . $month . ") OR (year(date_fin)=".$year." and month(date_fin)=" . $month .")) and id_propriete=" . $id_propriete;
		
		$data = $this->execute($request);
	
		$days_in_this_month = $this->days_in_month(array('month'=>$month, "year"=>$year));
		
		if($id_propriete === '687'){
			$dates = [ 
				'2020-04-01'=>['nbr_jours'=>5, 'date_fin'=>'2020-04-06'],
				'2020-04-06'=>['nbr_jours'=>8, 'date_fin'=>'2020-04-14']
			];			
		}else{
			$dates = [];
		}

		
		foreach($data as $k=>$v){
			$date_debut = array("date"=>explode(" ", $v["date_debut"])[0], "time"=>explode(" ", $v["created"])[1]);
			$date_fin = array("date"=>explode(" ", $v["date_fin"])[0], "time"=>explode(" ", $v["created"])[1]);
			
			$date_diff = date_diff(date_create($date_debut["date"]), date_create($date_fin["date"]));
			$nbr_jours_total =  $date_diff->format('%R%a');
			
			$dates[$v["date_debut"]] = array(
												"nbr_jours"	=>	$nbr_jours_total,
												"date_fin"	=>	$date_fin["date"]
											);
				
		}
		//var_dump($dates);
		return $dates;
		
	}
	
	
	public function DrowDays($year, $month){
		$days_in_selected_month = $this->days_in_month( array('month'=>$month, "year"=>$year) );
		
		$tr = '
				<tr class="time">
					<td colspan="'.$days_in_selected_month.'">
						<div style="display:flex; justify-content:space-between">
							<div>
								<div class="btn-group calendar">
									<a style="padding: 12px 12px">
										<i class="far fa-calendar-alt"></i> 
										<span data-year="'.$year.'" data-month="'.$month.'" class="interval">'.$this->months[intval($month)-1].' - '.$year.'</span></a>
								</div>
							</div>
							
							<div>
								<div style="display: table-cell; margin-right: 7px" class="">
									<div class="btn-group calendar">
										<a style="padding: 12px 12px" data-counter="0" class="calendar-refresh" title="Ajourdhui"><i class="fas fa-sync-alt"></i> </a>
									</div>											
								</div>				
								<div style="display: table-cell; margin-right: 7px" class="">
									<div class="btn-group calendar">
										<a style="padding: 12px 12px" class="direction" data-action="preview" data-counter="0" title="Précédent"><i class="fa fa-chevron-left"></i></a>
										<a style="padding: 12px 12px" class="direction" data-action="next" data-counter="0"  title="Suivant"><i class="fa fa-chevron-right"></i></a>
									</div>											
								</div>
							</div>
						</div>
					</td>
				</tr>';
		
		$tr .= '<tr class="days">';
		for($i=0; $i<$days_in_selected_month; $i++){
			$tr.= '<td>'.($i+1).'</td>';
		}
		$tr .= '</tr>';
		
		return $tr;
	}	
	
	
	public function DrowTasks($year, $month, $id_vehicule){
		$days_in_selected_month = $this->days_in_month(array('month'=>$month, "year"=>$year));
		
		$tr = '<tr class="tasks">';
		$dates = $this->ReadDates($year, $month, $id_vehicule);
		
		$ignore = 1;

		for($i = 1; $i <= $days_in_selected_month; $i++){
			$day = ($i < 10)? "0".$i: $i;
			$date_debut = $year."-".$month."-".$day;
			
			if($ignore === 1){
				
				// If current date fin is the date debut for the next
				if(array_key_exists($date_debut, $dates)){
					$date_fin = $dates[$date_debut]["date_fin"];
					if(array_key_exists($date_fin, $dates)){
						$colaps = true;
						$next_ignore = $dates[$date_fin]["nbr_jours"]+1;
					}else{
						$colaps = false;
						$next_ignore = 0;
					}
				}else{
					$colaps = false;
					$next_ignore = 0;
				}
				
				
				
				if(array_key_exists($date_debut, $dates)){
					$colspan = $colaps===true? $dates[$date_debut]["nbr_jours"] + 1 + $next_ignore: $dates[$date_debut]["nbr_jours"] + 1;
					$ignore = $colspan;
					
					if($colaps){
						$td = "<table style='border:none'><tr style='border:none'>";
						for($k=1; $k<=$ignore; $k++){
							
							
							if($k === 1 ) $td .= "<td style='border:none' colspan='" . ($dates[$date_debut]["nbr_jours"] + 1) . "'><div class='task' style='margin:0px'>".$ignore."</div></td>";
							if($k === ($dates[$date_debut]["nbr_jours"] + 2))  $td .= "<td style='border:none' colspan='" . $next_ignore . "'><div class='task' style='margin:0px'>".$next_ignore."--</div></td>";
							
							
						}
						$td .= "</tr></table>";
					
						
						$label = $td;
					}else{
						$label = "<div class='task'>".$ignore."</div>";
					}
					
					
				}else{
					$colspan = 1;
					$label = $i;
				}
				$tr.= '<td class="add_contrat" colspan="'.$colspan.'">'.$label.'</td>';				
			}else{
				$ignore--;
				if($ignore === 0) $tr.= '<td>'.$i.'</td>';				
			}
		}
		
		$tr .= '</tr>';			

		return $tr;
	}
	
	
	public function Drow( $params ){
		$year = $params["year"];
		$month = intval($params["month"])<10? "0".intval($params["month"]): $params["month"];
		$html = '<div class="content "><table>';
		$proprietes = $this->find("", ["conditions"=>["is_for_location="=>1] ], "propriete");
		$html .= $this->DrowDays($year, $month);
		foreach($proprietes as $k=>$v){
			$html .= $this->DrowTasks($year, $month, $v["id"]);
		}
		$html .= '</table></div>';
		return $html;
		
	}


	public function drawCalendar_3($month,$year, $args=null){
		
		$calendar = "Error Creating Calendar ...!";
		$counter = $args["options"]["counter"];
		
		if(isset($args["options"]["style"]) && $args["options"]["style"] === "vehicule"){
			
			$counter = isset($args["options"]["counter"])? $args["options"]["counter"]:0;
			$days_in_selected_month = $this->days_in_month(($month + $counter), $year);
			
			$_month = (($month + $counter)>9)? ($month + $counter): "0". ($month + $counter);
			$first_day_date = $year . "-" . $_month . "-01";
			$last_day_date = $year . "-" . $_month . "-" . $days_in_selected_month;
			
			$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
			$calendar.= '<tr class="calendar-row">';
			$row = array();
			for($i=0; $i<$days_in_selected_month; $i++){
				$calendar.= '<td style="min-width:40px; font-size:10px" class="calendar-day-head"><span class="days">'.($i+1).'</span></td>';
			}
			$calendar.= '</tr>';
			
			$value="";
			$span = 1;
			//var_dump($args["data"]);
			foreach($args["data"] as $k=>$row){
				$value="";
				$span = 1;
				$i = 0;
				//var_dump($row);
				foreach($row as $day=>$content){
					$i++;
					if($value === $content){
						
						if($i === count($row)){
							$extract = explode(";",explode("|",$value)[0]);
							
							if( $extract[0] === "empty"){
								$calendar.= '<td style="border-right:1px solid #ccc; max-width:40px; padding:10px 0px" colspan='.$span.'></div></td>';
							}else{
								$calendar.= '<td style="border-right:1px solid #ccc; max-width:40px; padding:10px 0px" colspan='.$span.'><div class="label label-green" style="padding:10px 5px; font-size:10px;  ">' . $extract[0] . '</div></td>';
							}
							
							//$calendar.= '<td style="border-right:1px solid #ccc; max-width:40px; padding:4px 0px" colspan='.$span.'><div class="label label-green" style="padding:3px 5px; font-size:10px; background-color:' . $extract[1] . '">' . $extract[0] . '--</div></td>';
						}else{
							$span += 1;
							$value = $content;
						}
					}else{
						if($day === 1){
							$span = 1;
							$value = $content;
						}else{
							$extract = explode(";",explode("|",$value)[0]);
								//var_dump($extract);
							if( $extract[0] === "empty"){
								$calendar.= '<td style="border-right:1px solid #ccc; max-width:40px; padding:10px 0px" colspan='.$span.'></div></td>';
							}else{
								$calendar.= '<td style="border-right:1px solid #ccc; max-width:40px; padding:10px 0px" colspan='.$span.'><div class="label label-green" style="padding:10px 5px; font-size:10px;  ">' . $extract[0] . '</div></td>';
							}
							
							$span = 1;
							$value = $content;								
						}
					}
					
				}
				$calendar.= '</tr>';
			}
			//var_dump($row);
			$calendar.= '</table>';
			
		}elseif(isset($args["options"]["style"]) && $args["options"]["style"] === "month"){
			/* draw table */
			$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

			/* table headings */
			$headings = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
			$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
			$month += $counter;
			/* days and weeks vars now ... */
			$running_day = date('w',mktime(0,0,0,$month,1,$year)); 		// order of first day in the week
			$days_in_month = date('t',mktime(0,0,0,$month,1,$year)); 	// number of days in given month
			$days_in_this_week = 1;
			$day_counter = 0;
			$dates_array = array();

			/* row for week one */
			$calendar.= '<tr class="calendar-row">';

			for($x = 0; $x < $running_day; $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
				$days_in_this_week++;
			endfor;

			for($list_day = 1; $list_day <= $days_in_month; $list_day++):
				$calendar.= '<td class="calendar-day">';
				$day = ($list_day<10)? "0" . $list_day: $list_day;
				$_month = ($month>9)? $month: "0". $month;
				$date = $year . "-" . $_month . "-" . $day;
			
				$calendar.= '<div class="day-number">'.$list_day.'</div>';
				$i	= 	0;
				$j	=	0;
				$complexes = array();
			
				foreach($args["data"] as $k=>$v){

					if( $v["date_debut"] === $date || $v["date_fin"] === $date){
						if(!in_array($v["vehicule"], $complexes)){
							array_push($complexes,$v["vehicule"]);
							if($i<6){
								$calendar.= "<div class='label label-green' style='padding:2px 3px; font-size:10px;background-color:" .  $v["color"] . "'>" .  $v["vehicule"] . "</div>";
							}else{
								$j++;
							}
							$i++;							
						}
						

					}else{
						/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
					//$calendar.= str_repeat('<p></p>',1);
					}				
				}

				if($j>0){
					$calendar.= "<div class='label label-default' style='padding:2px; font-size:10px;'>" .  $j . " Autres</div>";
				}
			
				$calendar.= '</td>';
				if($running_day == 6):
					$calendar.= '</tr>';
					if(($day_counter+1) != $days_in_month):
						$calendar.= '<tr class="calendar-row">';
					endif;
					$running_day = -1;
					$days_in_this_week = 0;
				endif;
				$days_in_this_week++; $running_day++; $day_counter++;
			endfor;

			/* finish the rest of the days in the week */
			if($days_in_this_week < 8):
				for($x = 1; $x <= (8 - $days_in_this_week); $x++):
					$calendar.= '<td class="calendar-day-np"> </td>';
				endfor;
			endif;

			$calendar.= '</tr>';
			$calendar.= '</table>';	
			
		}

		return $calendar;
	}
	
	
	public function drawCalendar_2($month,$year, $args=null){
		$table = '<table class="calendar">';
	}
	
	
	/*****************************
			MY CALENDAR
	*****************************/
	
	public function Get($params = []){

		$counter = isset($params['counter'])? $params['counter']: 0;
		$today = date("Y-m-d");
		$month = intval(date("m", strtotime("$today ".$counter." month")));
		$year = intval(date("Y", strtotime("$today ".$counter." month")));
		
		$style = isset($params['style'])? intval($params['style']): 1;

		$template = '
			<div class="mycalendar-body-header">
				<div class="title"> {{type}} </div>
				<div class="navigation">
					<ul>
						<li><a class="pre"><i class="fa fa-chevron-left"></i></a></li>
						<li><a class="current" data-counter="{{counter}}"><i class="far fa-calendar"></i> {{date}}</a></li>
						<li><a class="next"><i class="fa fa-chevron-right"></i></a></li>
					</ul>
				</div>
			</div>
			{{complexe}}
			<div class="mycalendar-table">
				{{body}}
			</div>
		';

		$replace_this = ['{{type}}', '{{date}}', '{{body}}', '{{counter}}', '{{complexe}}'];
		$complexe = '';
		
		if($style === 1){
			$template = str_replace($replace_this, ['Par Mois', $this->months[intval($month)-1] . " - " . $year, $this->By_Month(['month'=>$month, 'year'=>$year]), $counter, "" ], $template);
		}elseif($style === 2){
			
			$complexe = '
				<div class="d-flex">
					<div class="complexe">
						<select id="id_complexe">
							<option value="-1">-- Complexe </option>
							{{options}}
						</select>
					</div>
					<div class="client">
						<select id="UID">{{clients}}</select>
					</div>
				</div>
			';
			$options = '';
			$complexes = $this->find('', ['order'=>'name DESC'], 'complexe');
			foreach($complexes as $k=>$v){
				
				if(isset($params["id_complexe"])){
					if($params["id_complexe"] === $v["id"]){
						$options .= '<option selected value="'.$v["id"].'">'. strtoupper($v["name"]).'</option>';
					}else{
						$options .= '<option value="'.$v["id"].'">'. strtoupper($v["name"]).'</option>';
					}
				}else{
					$options .= '<option value="'.$v["id"].'">'. strtoupper($v["name"]).'</option>';
				}
				
			}
			
			if(isset($params["id_complexe"])){
				if(isset($params["UID"])){
					$complexe = str_replace(['{{options}}','{{clients}}'], [$options, $this->Get_Client_By_Complexe(['id_complexe'=>$params["id_complexe"], 'UID'=>$params["UID"]])] , $complexe);
				}else{
					$complexe = str_replace(['{{options}}','{{clients}}'], [$options, $this->Get_Client_By_Complexe(['id_complexe'=>$params["id_complexe"]])] , $complexe);
				}
				
			}else{
				$complexe = str_replace(['{{options}}','{{clients}}'], [$options, ""] , $complexe);
			}
			
			
			
			$id_complexe = isset($params["id_complexe"])? $params["id_complexe"]:0;
			
			$template = str_replace($replace_this, ['Par Société', $this->months[intval($month)-1] . " - " . $year, $this->By_Societe(['month'=>$month, 'year'=>$year, 'id_complexe'=>$id_complexe]), $counter, $complexe ], $template);
			
		}elseif($style === 3){
			$template = str_replace($replace_this, ['Par Mois', $this->months[intval($month)-1] . " - " . $year, "body", $counter, "" ], $template);
		}
		
		
		
		
		return $template;
	}
	
	public function Get_Client_By_Complexe($params){
		$id_complexe = $params['id_complexe'];
		$request = "
					SELECT 
						client.id, 
						client.first_name, 
						client.last_name, 
						client.societe_name as client, 
						v_propriete.name as complexe_name, 
						v_propriete.id_complexe,
						contrat.UID as UID
					FROM client
					JOIN contrat on contrat.id_client = client.id
					JOIN propriete_location on propriete_location.UID = contrat.UID
					JOIN v_propriete on propriete_location.id_propriete = v_propriete.id
					WHERE v_propriete.id_complexe=".$id_complexe." and year(contrat.created)=2021 
					GROUP BY client.societe_name 
					ORDER BY client.societe_name
		";
		//echo $request;
		$data = $this->execute($request);
		
		$reaturned = '<option selected value="-1">Client --</option>';
		foreach($data as $k=>$v){
			if(isset($params["UID"]))
				if($params["UID"] === $v["UID"])
					$reaturned .= '<option selected value="'.$v["id"].'">'.$v["client"].'</option>';
				else
					$reaturned .= '<option value="'.$v["id"].'">'.$v["client"].'</option>';
			else
				$reaturned .= '<option value="'.$v["id"].'">'.$v["client"].'</option>';
		}
		return $reaturned;
		
	}
	
	public function By_Month($params = []){
		
		$month = intval(isset($params['month'])? $params['month']: date('m'));
		$year = intval(isset($params['year'])? $params['year']: date('Y'));

		$request = "select * from v_contrat_periode where (year(date_debut)=".$year." and month(date_debut)=" . intval($month) .") OR (year(date_fin)=".$year." and month(date_fin)=" . intval($month) .") order by date_debut, date_fin";
		$_data = $this->execute($request);
		$data = array();
		foreach($_data as $k=>$v){
			array_push($data, array(
				"societe_name"	=>	$v["societe_name"],
				"date_debut"	=>	$v["date_debut"],
				"date_fin"		=>	$v["date_fin"],
				"color"			=>	$v["color"]
			));
		}
		
		
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		$headings = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

		$running_day = date('w',mktime(0,0,0,$month,1,$year)); 		// order of first day in the week
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year)); 	// number of days in given month
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		$calendar.= '<tr class="calendar-row">';

		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
			$days_in_this_week++;
		endfor;

		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
			$calendar.= '<td class="calendar-day">';

			$day = ($list_day<10)? "0" . $list_day: $list_day;
			$_month = ($month>9)? $month: "0".$month;
			$date = $year . "-" . $_month . "-" . $day;
			$calendar.= '<div class="day-number">'.$list_day.'</div>';
			$i	= 	0;
			$j	=	0;
			$complexes = array();
			$hided = "";
		
			foreach($data as $k=>$v){
				if( $v["date_debut"] === $date || $v["date_fin"] === $date){
					if(!in_array($v["societe_name"], $complexes)){
						array_push($complexes,$v["societe_name"]);
						if($i<6){
							$calendar.= "<div class='label label-green' style='padding:2px 3px; font-size:10px;background-color:" .  $v["color"] . "'>" .  $v["societe_name"] . "</div>";
						}else{
							$j++;
							$hided .= "<div class='label label-green' style='padding:2px 3px; font-size:10px;background-color:" .  $v["color"] . "'>" .  $v["societe_name"] . "</div>";
						}
						$i++;							
					}
				}							
			}

			if($j>0){
				$calendar.= "<div class='label label-default calendar_dev' style='padding:2px; font-size:10px;'>" .  $j . " Autres </div>";
				$calendar.= "<div class='hide to_show'>" .  $hided . "</div>";
			}

			$calendar.= '</td>';
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;
		
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
			endfor;
		endif;
		
		$calendar.= '</tr>';		
		$calendar.= '</table>';	
		
		return $calendar;
		
		
	}
	
	public function By_Societe($params = []){
		
		$month = isset($params['month'])? $params['month']: date('m');
		$year = isset($params['year'])? $params['year']: date('Y');

		$days_in_selected_month = $this->days_in_month(['month'=>intval($month), 'year'=>intval($year)]);
		$_month = ( $month >9 )? $month : "0". $month;
		$first_day_date = $year . "-" . $_month . "-01";
		$last_day_date = $year . "-" . $_month . "-" . $days_in_selected_month;

		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
		$calendar.= '<tr class="calendar-row">';
		$row = array();
		for($i=0; $i<$days_in_selected_month; $i++){
			$calendar.= '<td class="calendar-day-head"><span class="days">'.($i+1). '</span></td>';
		}
		$calendar.= '</tr>';

		$value="";
		$span = 1;
		$counter = 0;
		foreach($this->Data_Of_By_Societe(['month'=>$month, 'year'=>$year, 'id_complexe'=>$params["id_complexe"]]) as $k=>$row){
			$value="";
			$span = 1;
			$calendar.= '<tr class="tickets">';
			foreach($row as $day=>$content){

				if($value === $content){
					$span += 1;
					$value = $content;
				}else{
					if($day === 1){
						$span = 1;
						$value = $content;
					}else{
						$extract = explode(";",explode("|",$value)[0]);
							//var_dump($extract);
						$bg_color = explode("-", $extract[1]);
						if( $extract[0] === "empty"){
							$calendar.= '<td style="border-right:1px solid #ccc; padding:4px 0px" colspan='.$span.'><div class="label label-green" style="font-size:8px; padding:3px 5px; background-color:' . $bg_color[0] . '">vide</div></td>';
						}else{
							$calendar.= '<td style="border-right:1px solid #ccc; padding:4px 0px" colspan='.$span.'><div class="label label-green ticket" data-id_location="'.$bg_color[2].'" data-id_client="'.$bg_color[1].'" style="font-size:8px; padding:3px 5px; border-right:5px solid red; border-left:5px solid yellow; background-color:' . $bg_color[0] . '">' . $extract[0] . '</div></td>';
						}

						$span = 1;
						$value = $content;								
					}


				}
			}
			$counter++;
			$calendar.= '</tr>';
		}
		
		if($counter === 0){
			for($j=0; $j<10; $j++){
				$calendar.= '<tr>';
				$row = array();
				for($i=0; $i<$days_in_selected_month; $i++){
					$calendar.= '<td></td>';
				}
				$calendar.= '</tr>';
			}
		}
		
		//var_dump($row);
		$calendar.= '</table>';
		return $calendar;
	}
	
	public function Data_Of_By_Societe($params = []){
		
		$current_month = intval($params['month']);//date('m');
		$current_year = intval($params['year']); //date('Y');

		$days_in_selected_month = $this->days_in_month(['month'=>$current_month, 'year'=>$current_year]);

		$listOfAppartements = array();
		$listOfDates = array();

		$id_complexe = isset($params["id_complexe"])? $params["id_complexe"]:0;
		$request = "
					select client.first_name, client.last_name, client.societe_name as client , v_propriete.name as complexe_name, v_propriete.id_complexe,contrat.UID as UID,v_propriete.propriete_category
					from client
					join contrat on contrat.id_client = client.id
					JOIN propriete_location on propriete_location.UID = contrat.UID AND propriete_location.source='contrat'
					JOIN v_propriete on propriete_location.id_propriete = v_propriete.id
					where v_propriete.id_complexe=".$id_complexe." group by client.societe_name order by client.societe_name";
		//echo $request;
		$data_ = $this->execute($request);

		foreach($data_ as $kk=>$vv){

			$request = "select * from v_propriete_location_1 where ((year(date_debut)=".$current_year." and month(date_debut)=" . intval($current_month) .") OR (year(date_fin)=".$current_year." and month(date_fin)=" . intval($current_month) .")) AND id_complexe=".$id_complexe."  order by id_client,code, date_debut, date_fin";
			$data = $this->execute($request);

			$sub="";
			$date_debut = "";
			$date_fin = "";
			$code = "";
			$color = "";
			$listOfDates = array();
			//var_dump($data);
			foreach($data as $k=>$v){
				if( array_key_exists($v["code"]." (".$v["propriete_category"].") ".$sub, $listOfAppartements) ){
					array_push($listOfDates,[ $v["date_debut"]=>$v["date_fin"] ]);
					if($k === (count($data)-1)){
						$listOfAppartements[$v["code"]." (".$v["propriete_category"].") ".$sub] = $listOfDates;
						$code="";
					}
				}else{
					if($k === 0){
						array_push($listOfDates,[ $v["date_debut"]=>$v["date_fin"] ]);
						$sub = ";".$v["hex_string"]."-".$v["id_client"]."-".$v["id"];
						$code = $v["code"]." (".$v["propriete_category"].") ";
						$listOfAppartements[$code.$sub] = $listOfDates;
					}else{
						$listOfAppartements[$code.$sub] = $listOfDates;
						$listOfDates = array();

						array_push($listOfDates,[ $v["date_debut"]=>$v["date_fin"] ]);
						$sub = ";".$v["hex_string"]."-".$v["id_client"]."-".$v["id"];
						$code = $v["code"]." (".$v["propriete_category"].") ";

						$listOfAppartements[$v["code"]." (".$v["propriete_category"].") ".$sub] = $listOfDates;

					}			
				}
			}
		}
		//var_dump($listOfAppartements);
		$row = array();

		$listOfRows = array();
		foreach($listOfAppartements as $code=>$dates){

			for($i=1;$i<=$days_in_selected_month;$i++){
				$row[$i] = "empty;#ededed";
			}

			$day = "01";
			$_month = $current_month > 9? $current_month: "0". $current_month;
			$start =  new DateTime($current_year . "-" . $_month . "-" . $day);
			$last = new DateTime($current_year . "-" . $_month . "-" . $days_in_selected_month);

			foreach($dates as $k=>$v){

				foreach($v as $kk=>$vv){
					$date_debut = new DateTime($kk);
					$date_fin = new DateTime($vv);				
				}

				if($date_debut <= $start){
					$diff = $start->diff($date_fin)->days;
					$days = $date_debut->diff($last)->days;

					if($diff>$days_in_selected_month){
						for($i=1; $i<=$days_in_selected_month; $i++){
							$row[$i] = $code."|".$k;
						}
					}else{
						for($i=1; $i<=$diff;$i++){
							$row[$i] = $code."|".$k;
						}
					}

				}elseif($date_fin >= $last){
					//echo "fin >= endofday";
					$diff = $start->diff($date_debut)->days;
					$diff +=1;
					$days = $date_debut->diff($last)->days;
					for($i=$diff; $i<($diff+$days);$i++){
						$row[$i] = $code."|".$k;
					}

				}else{
					$diff = $date_debut->diff($start)->days; // calculat day past from the first day in month
					$days = $date_debut->diff($date_fin)->days; // number of days reserved
					$diff +=1;
					for($i=$diff; $i<($diff+$days);$i++){
						$row[$i] = $code."|".$k;
					}
				}
			}

			array_push($listOfRows,$row);
		}

		return $listOfRows;
		
	}
	
	
}

$calendar = new Calendar;