<?php
require_once('Helpers/Modal.php');
require_once('Helpers/View.php');

class Depense extends Modal{
	
	private $tableName = __CLASS__;
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName(strtolower($this->tableName));
			
			foreach($this->find('', [], 'propriete') as $k=>$v){
				if(count($this->find('', ['conditions'=>['id_propriete='=>$v['id']]], 'status_of_propriete')) === 0 ){
					$data = [
						'created'				=>	$v['created'],
						'created_by'			=>	$v['created_by'],
						'id_propriete_status'	=>	$v['id_propriete_status'],
						'id_propriete'			=>	$v['id']
					];	
					$this->save($data, 'status_of_propriete');
				}

			}
			
			
		}catch(Exception $e){
			die($e->getMessage());
		}
	}	
	
	public function getColumns($style = null){
		
		$style = (is_null($style))? strtolower($this->tableName): $style;
		
		$columns = array();
		$l = new ListView();
		foreach($l->getDefaultStyle($style, $columns)["data"] as $k=>$v){
			array_push($columns, array("column" => $v["column"], "label" => $v["label"], "style"=>$v["style"], "display"=>$v["display"], "format"=>$v["format"]) );
		}
		array_push($columns, array("column" => "actions", "label" => "", "style"=>"min-width:105px; width:105px", "display"=>1) );
		return $columns;
		
	}
		
	public function drawTable($args = null, $conditions = null, $useTableName = null){

		$showPerPage = array("20","50","100","200","500","1000");
		
		$status = array(
			0	=>	"<div class='label label-red'>Désactivé</div>", 
			1	=>	"<div class='label label-green'>Activé</div>");
		
		$remove_sort = array("actions");
		
		
		$p_p = (isset($args['p_p']))? $args['p_p']: $showPerPage[0];
		$current = (isset($args['current']))? $args['current']: 0;
		$sort_by = (isset($args['sort_by']))? $args['sort_by']: "created";
		
		$temp = explode(" ", $sort_by );
		$order = "";
		if(count( $temp ) > 1 ){ $order =  $temp[1]; }
		
		$values = array("Error : " . $this->tableName);
		$t_n = ($useTableName===null)? strtolower($this->tableName): $useTableName;
		$column_style = (isset($args['column_style']))? $args['column_style']: $t_n;
		$total = 0;
		$totalItems = 0;
		if($conditions === null){
			$values = $this->find(null,array("order"=>$sort_by,"limit"=>array($current*$p_p,$p_p)),$t_n);
			foreach($value as $k=>$v) {
				$total += $v["montant"];
				$totalItems++;
			}
			//$totalItems = $this->getTotalItems();
		}else{
			$conditions["order"] = $sort_by;
			
			foreach($this->find(null,$conditions,$t_n) as $k=>$v) {
				$total += $v["montant"];
				$totalItems++;
			}
			$conditions["limit"] = array($current*$p_p,$p_p);
			$values = $this->find(null,$conditions,$t_n);
		}
		
		$returned = '<div class="col_12" style="padding: 0">';
	
		$returned .= '	<div style="display: flex; flex-direction: row">';
		$returned .= '		<div style="flex: auto; padding: 15px 0 10px 5px; margin: 0; color: rgba(118,17,18,1.00)">';
		$returned .= '			Total : ('.count($values).' / '.$totalItems.') <span class="current hide">'.$current.'</span>';
		$returned .= '		</div>';
		$returned .= '		<div style="width: 15rem">';
		$returned .= '			<div style="flex-direction: row; display: flex">';
		
		$returned .= '				<div class="text-center pr-10" style="padding-top:2px">';
		$returned .= '					<button class="btn btn-blue p-10 depense_chart"><i class="far fa-chart-bar"></i></button>';
		$returned .= '				</div>';
		
		$returned .= '				<div style="flex: 1">';
		$returned .= '					<select id="showPerPage">';
		
		foreach($showPerPage as $kk => $vv)
			$returned .= '					<option value="'.$vv.'" ' . ( $p_p == $vv ? "selected" : "") .'>'.$vv.'</option>';
		
		
		$returned .= '					</select>';
		$returned .= '					<span class="hide ' . $order . '" id="sort_by">'.$sort_by.'</span>';
		$returned .= '				</div>';
		
		$returned .= '				<div style="flex: 1; text-align: center">';
		$returned .= '					<div class="btn-group">';
		$returned .= '						<a style="padding: 12px 12px" id="btn_passive_preview"  title="Précédent"><i class="fa fa-chevron-left"></i></a>';
		$returned .= '						<a style="padding: 12px 12px" id="btn_passive_next" title="Suivant"><i class="fa fa-chevron-right"></i></a>';
		$returned .= '					</div>';
		$returned .= '				</div>';
		
		$returned .= '			</div>';
		$returned .= '		</div>';
		$returned .= '	</div>';	
	
		$returned .= '	<div style="font-size:18px" class="pt-15 pb-15 red text-center"><strong>Total : ' . $this->format($total) . '</div>';
		
		$returned .= '	<div class="panel" style="overflow: auto;">';
		$returned .= '		<div class="panel-content" style="padding: 0">';
		
		$returned .= '			<table class="table">';
		$returned .= '				<thead>';
		
		$t = explode("_",$this->tableName);
		$_t = "";
		foreach ($t as $k=>$v){
			$_t .= ($_t==="")? ucfirst($v): "_".ucfirst($v) ;
		}
		
		$l = new ListView();
		$defaultStyleName = $l->getDefaultStyleName($column_style);
		
		$columns = $this->getColumns($column_style);
		

		
		foreach($columns as $key=>$value){

			$style = ""; 
			$is_sort = ( in_array($value["column"], $remove_sort) )? "" : "sort_by";
			$is_display = ( isset($value["display"]) )? ($value["display"])? "" : "hide" : "";
			
			$label = ($value['column'] === "actions")? "<button data-default='".$defaultStyleName."' value='".$column_style."' class='show_list_options' style='float:right; background:none; border:none; color:white; '><i class='fas fa-ellipsis-h'></i></button>": $value['label'];
			
			if($is_sort === ""){
				$returned .= "<th class='".$is_sort. " ". $is_display . "' data-sort='" . $value['column'] . "'> " . $label. "</th>";
			}else{
				$returned .= "<th class='".$is_sort. " ". $is_display . "' data-sort='" . $value['column'] . "'> <i class='fas fa-sort'></i> " . $label . "</th>";
			}

		}
		$returned .= '					</tr>';
		$returned .= '				</thead>';
		$returned .= '				<tbody>';
		
		$content = '<div class="info info-success"><div class="info-success-icon"><i class="fa fa-info" aria-hidden="true"></i> </div><div class="info-message">Liste vide ...</div></div>';
		$i = 0;
		
		$t = explode("_",$this->tableName);
		$_t = "";
		foreach ($t as $k=>$v){
			$_t .= ($_t==="")? ucfirst($v): "_".ucfirst($v) ;
		}
		
		foreach($values as $k=>$v){
		$background = (isset($v["color"]))? $v["color"]: "";
		$returned .= '					<tr style="background-color:'.$background.'" data-page="'.$_t.'">';
			foreach($columns as $key=>$value){
				$is_display = ( isset($value["display"]) )? ($value["display"])? "" : "hide" : "";
				$style = (isset($columns[$key]["style"]))? $columns[$key]["style"]:"";
				
				
				
				if(isset($v[ $columns[$key]["column"] ])){
					if($columns[$key]["column"] == "id"){
						$returned .= "<td class='".$is_display."' style='".$style."'><span class='id-ligne'>" . $v[ $columns[$key]["column"] ] . "</span></td>";
					}elseif($columns[$key]["column"] == "status"){
						$returned .= "<td class='".$is_display."' style='".$style."'>" . $status[ $v["status"] ] . "</td>";
					}elseif($columns[$key]["column"] == "created"){
						$date = explode(" ", $v[ $columns[$key]["column"] ]);
						$notes = ($v["notes"])? '<i style="color:red; border-radius:50%;" class="fas fa-info-circle"></i>': '';
						
						if(count($date)>1){
							$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]. " " . $notes . "</div>";
						}else{
							$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
						}

						$returned .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";
					}elseif( $columns[$key]["column"] == "name"){
						if($v["appartement_number"] !== "0"){
							$returned .= "<td class='".$is_display."' style='".$style.";'>".$v["name"]." <div class='label label-blue'> App. ".$v["code"]."</div></td>";
						}else{
							$returned .= "<td class='".$is_display."' style='".$style.";'>".$v["name"]."</div></td>";
						}
						
					}else{
						if(isset($columns[$key]["format"])){
							if($columns[$key]["format"] === "money"){
								$returned .= "<td class='".$is_display."' style='".$style."'>" . $this->format($v[ $columns[$key]["column"] ]) . "</td>";
							}else if($columns[$key]["format"] === "on_off"){
								$returned .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
							}else if($columns[$key]["format"] === "color"){
								$returned .= "<td class='".$is_display."' style='".$style."'> <span style='padding:10px 15px; background-color:".$v[ $columns[$key]["column"] ]."'>".$v[ $columns[$key]["column"] ] . "</span></td>";
							}else if($columns[$key]["format"] === "date"){
								$date = explode(" ", $v[ $columns[$key]["column"] ]);
								if(count($date)>1){
									$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]."</div>";
								}else{
									$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
								}
								$returned .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";
								
							}else{
								$returned .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]. "</td>";
							}
						}else{
							$returned .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]."</td>";
						}
					}										
				}else{
					if($columns[$key]["column"] === "actions"){
						$returned .=   "<td style='".$style."'><button style='margin-right:10px' data-page='".$_t."' class='btn btn-red remove_ligne' value='".$v["id"]."'><i class='fas fa-trash-alt'></i></button><button data-action='edit' data-page='".$_t."' class='btn btn-orange show_form_right_container' value='".$v["id"]."'><i class='fas fa-edit'></i></button></td>";												
					}elseif($columns[$key]["column"] === "nbr"){
						$returned .=  "<td class='".$is_display."' style='".$style."'>0</td>";
					}else{
						if(isset($columns[$key]["format"])){
							if($columns[$key]["format"] === "money"){
								$returned .= "<td class='".$is_display."' style='".$style."'>" . $this->format(0) . "</td>";
							}else if($columns[$key]["format"] === "on_off"){
								$returned .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
							}else if($columns[$key]["format"] === "color"){
								$returned .= "<td class='".$is_display."' style='".$style."'></td>";
							}else{
								$returned .= "<td class='".$is_display."' style='".$style."'></td>";
							}
						}else{
							$returned .= "<td class='".$is_display."' style='".$style."'></td>";
						}
					}
				}


			}
			$returned .= '					</tr>';
		$i++	;
		}
	
		if($i == 0){
			$returned .= "<tr><td colspan='" . (count($columns)+1) . "'>".$content."</td></tr>";
		}
		
	
		$returned .= '				</tbody>';
		$returned .= '			</table>';
		$returned .= '		</div>';
		$returned .= '	</div>';
		$returned .= '</div>';
		echo $returned;

	}

	public function Get_Sum_By_Year_Month($params = []){
		$return = [];
		
		$month = isset($params["month"])? $params["month"]: date("m");
		$year = isset($params["year"])? $params["year"]: date("Y");
		
		$conditions = [ 'conditions AND' => [ 'YEAR(created) = ' => $year, 'MONTH(created) = ' => $month ] ];

		$request = '
		SELECT
			SUM(montant) AS `total`,
			MONTH(
				created
			) AS `month`,
			YEAR(
				created
			) AS `year`
		FROM
			depense
			';
		
		if(isset($params['year']))
			$request .= '
		WHERE YEAR(created) = ' . $year;
		
		$request .='
		GROUP BY
			MONTH(
				created
			),
			YEAR(
				created
			)
	';

		return $this->execute($request); // find('', $conditions , 'v_depense');
		
		//return json_encode($return, true);
		
	}
	
	public function Graph_01($params){
		$months = [
						1	=>	"Jan",
						2	=>	"Fév",
						3	=>	"Mars",
						4	=>	"Avr",
						5	=>	"Mai",
						6	=>	"Juin",
						7	=>	"Juil",
						8	=>	"Août",
						9	=>	"Sept",
						10	=>	"Oct",
						11	=>	"Nov",
						12	=>	"Déc"
					];
		
		$json = [
						1	=>	["month" => "Jan", "total"	=> 0],
						2	=>	["month" => "Fév", "total"	=> 0],
						3	=>	["month" => "Mars", "total"	=> 0],
						4	=>	["month" => "Avr", "total"	=> 0],
						5	=>	["month" => "Mai", "total"	=> 0],
						6	=>	["month" => "Juin", "total"	=> 0],
						7	=>	["month" => "Juil", "total"	=> 0],
						8	=>	["month" => "Août", "total"	=> 0],
						9	=>	["month" => "Sept", "total"	=> 0],
						10	=>	["month" => "Oct", "total"	=> 0],
						11	=>	["month" => "Nov", "total"	=> 0],
						12	=>	["month" => "Déc", "total"	=> 0]
					];
		
		
		$data = $this->Get_Sum_By_Year_Month($params);
		//var_dump($data);
				
		foreach($data as $k=>$v){
			$json[$v["month"]]["total"] = $v["total"];
		}
		
		return $json;
	}
	
	public function Draw_Graph_01(){

		$template = '
						<div class="panel graph">
							<div class="panel-content white text-center pt-10">
								<div class="d-flex">
									<div style="display: table-cell; margin-right: 7px" class="">
										<div class="btn-group">
											<a style="padding: 12px 12px" data-year="{{year}}" class="depense_chart after_load" title="Ajourd\'hui"><i class="fas fa-sync-alt"></i> </a>
										</div>											
									</div>				
									<div style="display: table-cell; margin-right: 7px" class="">
										<div class="btn-group">
											<a style="padding: 12px 12px" class="depense_direction" data-step="-1" title="Précédent"><i class="fa fa-chevron-left"></i></a>
											<a style="padding: 12px 12px" class="depense_direction" data-step="+1"  title="Suivant"><i class="fa fa-chevron-right"></i></a>
										</div>
									</div>
									<div style="display: table-cell; margin-right: 7px" class="">
										<div class="btn-group">
											<a style="padding: 12px 12px"><i class="far fa-calendar-alt"></i> <span class="depense_label">{{year}}</span></a>
										</div>
									</div>
								</div>

								<div style="max-width: 850px; height: auto" class="pt-15">
									<canvas id="bchart" class="p-0"></canvas>
								</div>

							</div>

						</div>
		
		';
		
		return str_replace("{{year}}", date('Y'), $template);
		
	}
	
	
	public function Table($params = []){
		
		
		$column_style = (isset($params['column_style']))? $params['column_style']: strtolower($this->tableName);
		
		$filters = (isset($params["filters"]))? $params["filters"]: [];
		
		$l = new ListView();
		$defaultStyleName = $l->getDefaultStyleName($column_style);
		$columns = $this->getColumns($column_style);
		
		
		$table = '
			<div class="table-container">
				<div class="d-flex space-between" style="padding:0 10px 10px 10px">
					<div style="font-size:16px; font-weight:bold">{{counter}}</div>
					<div class="text-green" style="font-size:16px; font-weight:bold">{{total}}</div>
				</div>
				<table id="depensetable">	
					<thead>	
						<tr>
							{{ths}}
						</tr>
						
					</thead>
					<tbody>
						{{trs}}
					</tbody>
				</table>
			</div>
		
		';
		
		/***********
			Columns
		***********/
		$ths = '';
		$trs_counter = 1;
		foreach($columns as $column){

			$style = ""; 
			$is_display = ( isset($column["display"]) )? ($column["display"])? "" : "hide" : "";

			if($column['column'] === "actions"){
				$ths .= "<th class='". $is_display . "'>";
				$ths .= "	<button data-default='".$defaultStyleName."' value='".$column_style."' class='show_list_options'>";
				$ths .= "		<i class='fas fa-ellipsis-h'></i></button>";
				$ths .= "	</button>";
				$ths .=	"</th>";
			}else{
				$trs_counter += $is_display === "hide"? 0:1;
				$ths .= "<th class='sort_by ". $is_display . "' data-sort='" . $column['column'] . "' data-sort_type='desc'>";
				$ths .=  "	<div class='d-flex'>";
				$ths .=  		$column['label'];
				$ths .= "		<i class='pl-5 fas fa-sort'></i> ";
				$ths .=  "	</div>";
				$ths .=	"</th>";
			}

		}
		
		/***********
			Conditions
		***********/
		
		$request = [];
		$sql = '';
		if(isset($params['request'])){
			if( $params['request'] !== "" ){
				if( isset($params['tags']) ){
					if( count( $params['tags'] ) > 0 ){
						foreach( $params['tags'] as $k=>$v ){
							$request[ 'LOWER(CONVERT(' . $v. ' USING latin1)) like '] = '%' . strtolower( $params['request'] ) . '%';
							
							$item = 'LOWER(CONVERT(' . $v. ' USING latin1)) like %' . strtolower( $params['request'] ) . '%';
							$sql .= $sql===''? $item.'<br>': ' AND '.$item.'<br>';
							
						}
					}
				}
			}
		}
		
		if( count($filters) > 0 ){
			foreach($filters as $k=>$v){
				if($v["value"] !== "-1"){
					if( $v["id"] === "Categorie" ){
						$request['id_category = '] = $v["value"];
						$item = 'id_category = ' . $v["value"];						
					}

					if( $v["id"] === "Complexe" ){
						$request['id_complexe = '] = $v["value"];
						$item = 'id_complexe = ' . $v["value"];						
					}
					
					if( $v["id"] === "Caisse" ){
						$request['id_caisse = '] = $v["value"];
						$item = 'id_caisse = ' . $v["value"];						
					}
					
					if( $v["id"] === "Mois" ){
						$request['MONTH(created) = '] = $v["value"];
						$item = 'MONTH(created) = ' . $v["value"];						
					}

					if( $v["id"] === "Années" ){
						$request['YEAR(created) = '] = $v["value"];
						$item = 'YEAR(created) = ' . $v["value"];						
					}

					$sql .= $sql===''? $item.'<br>': ' AND '.$item.'<br>';					
				}
				
			}

		}
		
		/***********
			Body
		***********/
		$use = (isset($params['use']))? strtolower($params['use']): strtolower($this->tableName);
		
		$conditions = [];
		
		if( count($request) === 1 ){
			$conditions['conditions'] = $request;
		}elseif( count($request) > 1 ){
			$conditions['conditions AND'] = $request;
		}
		
		if(isset($params['sort'])){
			$conditions['order'] = $params['sort'];
		}else{
			$conditions['order'] = 'created desc';
		}
		
		// Counter
		$total = 0;
		$counter = 0;
		foreach($this->find('', $conditions, $use) as $k=>$v){
			$counter++;
			$total += $v["montant"];
		}
		
		$pp = isset( $params['pp'] ) ? $params['pp']: 20;
		$current = isset( $params['current'] ) ? $params['current']: 0;
		$conditions['limit'] = [$current,$pp];
		
		$data = $this->find('', $conditions, $use);
		$trs = '';

		
		foreach($data as $k=>$v){
			
			$background = isset($v["all_ligne"])? $v["all_ligne"]? $v["hex_string"]: "": "";
			$trs .= '<tr style="background-color:'.$background.'" data-page="'.$use.'">';
			foreach($columns as $key=>$value){
				
				$style = (!$columns[$key]["display"])? "display:none": $columns[$key]["style"] ;
				$is_display = (!$columns[$key]["display"])? "hide": "" ;

				if(isset($v[ $columns[$key]["column"] ])){
					if($columns[$key]["column"] == "name"){
						$code = $v["code"] === ""? "": "<div style='font-size:10px; font-weight:bold'>" . $v["code"] . "</div>";
						$complexe = $v["name"] === ""? "": $v["name"] . $code;
						$trs .= "<td class='".$is_display."' style='".$style."'>" . $complexe . "</td>";
					}else{
					
						if(isset($columns[$key]["format"])){
							if($columns[$key]["format"] === "money"){
								$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format($v[ $columns[$key]["column"] ]) . "</td>";
							}else if($columns[$key]["format"] === "on_off"){
								$trs .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
							}else if($columns[$key]["format"] === "color"){
								$trs .= "<td class='".$is_display."' style='".$style."'> <span style='padding:10px 15px; background-color:".$v[ $columns[$key]["column"] ]."'>".$v[ $columns[$key]["column"] ] . "</span></td>";
							}else if($columns[$key]["format"] === "date"){
								$date = explode(" ", $v[ $columns[$key]["column"] ]);
								if(count($date)>1){
									$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]."</div>";
									$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
								}else{
									$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
								}
								$trs .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";

							}else{
								$trs .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]. "</td>";
							}
						}else{
							$trs .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]."</td>";
						}
					}	
				}else{
					if($columns[$key]["column"] == "actions"){
						$trs .=   "<td style='width:55px; text-align: center'><button data-controler='". $this->tableName ."' class='update' value='".$v["id"]."'><i class='fas fa-ellipsis-v'></i></button></td>";	
					
					}elseif($columns[$key]["column"] == "total"){
						$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format(0) . "</td>";
					}else{
						
						if($columns[$key]["format"] === "money"){
							$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format($v[ $columns[$key]["column"] ]) . "</td>";
						}else if($columns[$key]["format"] === "on_off"){
							$trs .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
						}else if($columns[$key]["format"] === "color"){
							$trs .= "<td class='".$is_display."' style='".$style."'> <span style='padding:10px 15px; background-color:".$v[ $columns[$key]["column"] ]."'>".$v[ $columns[$key]["column"] ] . "</span></td>";
						}else if($columns[$key]["format"] === "date"){
							$date = explode(" ", $v[ $columns[$key]["column"] ]);
							if(count($date)>1){
								$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]."</div>";
							}else{
								$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
							}
							
							$trs .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";
						}else{
							$trs .= "<td class='".$is_display."' style='".$style."'></td>";
						}						
					}

				}


			}
			$trs .= '</tr>';
			
		}
		
		if(count($data) === 0)
			$trs = '<tr><td colspan="'.$trs_counter.'">No Data to Display!</td></tr>';
		
		$total = "Total : " . $this->format($total);
		$counter = $counter . " Operations";
		return str_replace(["{{ths}}", "{{trs}}", "{{sql}}", "{{total}}", "{{counter}}"], [$ths, $trs, $sql, $total, $counter], $table);
		
	}
	
	public function ShortTable($depenses){
		
		$template = '
			
			<div class="depenses">
				<div class="d-flex space-between pb-10">
					<div class="title">Dépenses</div>
					<div style="font-wheight:bold: font-size:12px"> {{total}} </div>
				</div>
				{{items}}
			</div>
		
		';
		
		$items = '';
		$total = 0;
		foreach($depenses as $k=>$v){
			$date_depense = isset($v["date_depense"])? $v["date_depense"]: $v["created"];
			$total += $v["montant"];
			
			$items .= '
						<div class="item">
							<div class="d-flex space-between">
								<div class="date">'.$date_depense.'</div>
								<div class="montant">'.$this->format($v["montant"]).'</div>
							</div>
							<div class="description">'.$v["libelle"].'</div>
						</div>
			';
		}
		$empty = '<div class="label label-default"> Aucune Dépense! </div>';
		
		$items = $items === ''? $empty: $items;
		$total = $this->format($total);
		return str_replace(["{{items}}", "{{total}}"], [$items, $total], $template);
		
	}
	
	public function Create($params = []){
		$view = new View("depense.create");
		return $view->render(['params'=>$params]);
	}
	
	public function Update($params){
		
		$push = [];
		
		$depense = $this->find('', [ 'conditions'=>[ 'id='=>$params['id'] ] ], '');
		
		if( count($depense) > 0 ){
			$push['depense'] =	$depense[0];
			$propriete = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_propriete"] ] ], 'propriete');
			if( count($propriete) > 0 ){
				$push['propriete'] =	$propriete[0];
			}
			
			$societe = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_societe"] ] ], 'entreprise');
			if( count($societe) > 0 ){
				$push['societe'] =	$societe[0];
			}
			
			$person = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_person"] ] ], 'person');
			if( count($person) > 0 ){
				$push['person'] =	$person[0];
			}
		
			$contrat = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_contrat"] ] ], 'contrat');
			if( count($contrat) > 0 ){
				$push['contrat'] =	$contrat[0];
			}
			
			$client = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_client"] ] ], 'client');
			if( count($client) > 0 ){
				$push['client'] =	$client[0];
			}
			
			$proprietaire = $this->find('', [ 'conditions'=>['id='=> $depense[0]["id_proprietaire"] ] ], 'proprietaire');
			if( count($proprietaire) > 0 ){
				$push['proprietaire'] =	$proprietaire[0];
			}
			
		}
		
		
		$view = new View("depense.create");
		return $view->render($push);
	}
	
	public function Store($params){
		
		$created = date('Y-m-d H:i:s');
		$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
		
		$data = [
			'UID'				=>	addslashes($params['columns']['UID']),
			'created'			=>	$created,
			'created_by'		=>	$created_by,
			'updated'			=>	$created,
			'date_depense'		=>	$params['columns']['date_depense'],
			'id_caisse'			=>	$params['columns']['depense_caisse'],
			'id_propriete'		=>	$params['columns']['id_propriete'],
			'id_category'		=>	$params['columns']['depense_category'],
			'id_societe'		=>	$params['columns']['id_societe'],
			'id_person'			=>	$params['columns']['id_person'],
			'id_contrat'		=>	$params['columns']['id_contrat'],
			'id_client'			=>	$params['columns']['id_client'],
			'id_proprietaire'	=>	$params['columns']['id_proprietaire'],
			'montant'			=>	$params['columns']['montant'],
			'libelle'			=>	addslashes($params['columns']['libelle'])
		];
		
		if( isset($params['columns']["id"]) ){
			unset($data["created"], $data["created_by"]);
			$data["id"] = $params['columns']["id"];
		}
		
		if($this->save($data)){
			if(isset($data["id"])){
				$msg = $data["libelle"] . " Montant: " . $this->format($data["montant"]);
				$this->saveActivity("fr", $created_by, ['Depense', 0], $data["id"], $msg);				
			}else{
				$msg = $data["libelle"] . " Montant: " . $this->format($data["montant"]);
				$this->saveActivity("fr", $created_by, ['Depense', 1], $this->getLastID(), $msg);
			}

			return 1;
			
		}else{
			return $this->err;
		}		
		
	}
	
	public function Remove($params){
		if(isset($params["id"])){
			
			$data = $this->find('', ['conditions' => [ 'id=' => $params['id'] ] ], '');
			if(count($data) === 1){
				$data = $data[0];
				$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
				$msg = $data["libelle"] . " Montant: " . $this->format($data["montant"]);
				$this->saveActivity("fr", $created_by, ['Depense', -1], $data["id"], $msg);
				$this->delete($params["id"]);
				return 1;
			}else{
				return 0;
			}

		}else{
			return 0;
		}
	}
	
	public function FindBy($params){
		$source = [
						  "ActionScript",
						  "AppleScript",
						  "Asp",
						  "BASIC",
						  "C",
						  "C++",
						  "Clojure",
						  "COBOL",
						  "ColdFusion",
						  "Erlang",
						  "Fortran",
						  "Groovy",
						  "Haskell",
						  "Java",
						  "JavaScript",
						  "Lisp",
						  "Perl",
						  "PHP",
						  "Python",
						  "Ruby",
						  "Scala",
						  "Scheme"
						];
		return $source;
	}
	
}
$depense = new Depense;