<?php
require_once('Helpers/Modal.php');
require_once('Helpers/View.php');

class Propriete_Location extends Modal{

	private $columns = array(
		array("column" => "id", "label"=>"#ID", "style"=>"display:none", "display"=>0),
		array("column" => "created", "label"=>"CREATION", "style"=>"display:none", "display"=>0),
		array("column" => "code", "label"=>"CODE", "style"=>"background-color:rgb(92, 183, 243,0.1); font-weight:bold; min-width:105px; width:125px", "display"=>1),
		array("column" => "name", "label"=>"COMPLEXE", "style"=>"font-weight:bold", "display"=>1),
		array("column" => "proprietaire", "label"=>"PROPRIETAIRE", "style"=>"font-weight:bold", "display"=>1),
		array("column" => "periode", "label"=>"PERIODE(s)", "style"=>"font-weight:bold", "display"=>1),
		array("column" => "status", "label"=>"STATUS", "style"=>"min-width:80px; width:80px", "display"=>1),
		array("column" => "actions", "label"=>"", "style"=>"min-width:105px; width:105px", "display"=>1)
	);
	
// construct
	private $tableName = "Propriete_Location";
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName(strtolower($this->tableName));
		}catch(Exception $e){
			die($e->getMessage());
		}
	}	
	
		
	public function getColumns(){
		
		if ( isset($this->columns) ){
			return $this->columns;
		}else{
			$columns = array();
			$l = new ListView();
			foreach($l->getDefaultStyle("v_propriete_location")["data"] as $k=>$v){
				array_push($columns, array("column" => $v["column"], "label" => $v["label"], "style"=>$v["style"], "display"=>$v["display"]) );
				
			}
			array_push($columns, array("column" => "actions", "label" => "", "style"=>"min-width:105px; width:105px", "display"=>1) );
			return $columns;
		}
		
	}
	
	
	//	Draw Table
	public function drawTable($args = null, $conditions = null, $useTableName = null){

		$showPerPage = array("20","50","100","200","500","1000");
		$status = array("<div class='label label-red'>Désactivé</div>", "<div class='label label-green'>Activé</div>");
		$remove_sort = array("actions","nbr","periode");
		
		
		$p_p = (isset($args['p_p']))? $args['p_p']: $showPerPage[5];
		$current = (isset($args['current']))? $args['current']: 0;
		$sort_by = (isset($args['sort_by']))? $args['sort_by']: "created";
		$temp = explode(" ", $sort_by );
		$order = "";
		if(count( $temp ) > 1 ){ $order =  $temp[1]; }
		
		$values = array("Error : " . $this->tableName);
		$t_n = ($useTableName===null)? strtolower($this->tableName): $useTableName;
		
		if($conditions === null){
			$values = $this->find(null,array("order"=>$sort_by,"limit"=>array($current*$p_p,$p_p)),$t_n);
			$totalItems = $this->getTotalItems();
		}else{
			$conditions["order"] = $sort_by;
			$totalItems = count($this->find(null,$conditions,$t_n));
			$conditions["limit"] = array($current*$p_p,$p_p);
			$values = $this->find(null,$conditions,$t_n);
		}
		
		$returned = '<div class="col_12" style="padding: 0">';	
	
		$returned .= '	<div class="panel" style="overflow: auto;">';
		$returned .= '		<div class="panel-content" style="padding: 0">';
		
		$returned .= '			<table class="table">';
		$returned .= '				<thead>';
		$returned .= '					<tr>';
		$returned .= '						<th style="display:none">#ID</th> <th style="max-width:180px; width:180px; background-color: rgba(50, 115, 220, 0.3);">APPARTEMENT</th> <th>PERIODES</th><th style="width:105px"></th>';
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
			$returned .= '					<tr>';

			$periodes = count(explode(" ", $v["debuts"]));
			$debuts = explode(" ", $v["debuts"]);
			$fins = explode(" ", $v["fins"]);

			$strings = "";

			for( $p=0; $p < $periodes; $p++ ){

				if($p===0){
					$strings .= "<div class='dashed' style='display:inline-block; padding:5px'><span class='label label-default'>" . $debuts[$p] . "</span> <i class='fas fa-angle-double-right'></i> <span class='label label-green'>" . $fins[$p] . "</span></div>";
				}else{
					$strings .= "<div class='dashed' style='display:inline-block; margin-top:10px; margin-left:10px; padding:5px'><span class='label label-default'>" . $debuts[$p] . "</span> <i class='fas fa-angle-double-right'></i> <span class='label label-green'>" . $fins[$p] . "</span></div>";
				}

			}


				
			
			$returned .= "<td style='display:none'><span class='id-ligne'>" . $v['id'] . "</span></td>";
			$returned .= "<td style='background-color: rgba(50, 115, 220, 0.1); font-weight:bold; font-size:14px'>" . $v['code'] . "</td>";
			$returned .= "<td style=''>" . $strings . "</td>";
			$returned .= "<td style=''><button style='margin-right:10px' data-page='".$_t."' class='btn btn-red remove_ligne_propriete_location' value='".$v["id"]."'><i class='fas fa-trash-alt'></i></button><button data-page='".$_t."' class='btn btn-orange edit_ligne_propriete_location' value='".$v["id"]."'><i class='fas fa-edit'></i></button></td>";
			
			$returned .= '					</tr>';
		$i++	;
		}
	
		if($i == 0){
			$returned .= "<tr><td colspan='4'>".$content."</td></tr>";
		}
		
	
		$returned .= '				</tbody>';
		$returned .= '			</table>';
		$returned .= '		</div>';
		$returned .= '	</div>';
		$returned .= '</div>';
		echo $returned;

	}
	
	public function Store($params){
		
		$created = date('Y-m-d H:i:s');
		$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
		
		$data = [
			"UID"			=>	$params['UID'],
			"created"		=>	$created,
			"id_propriete"	=>	$params['id_propriete'],
			"source"		=>	"contrat",
			"id_periode"	=>	$params['id_periode'],
			"date_debut"	=>	$params['date_debut'],
			"date_fin"		=>	$params['date_fin'],
			"status"		=>	1
		];
		
		$this->save($data);
		
		$lastID = $this->getLastID();

		$msg = $params['date_debut'] . " " . $params['date_fin'];

		$this->saveActivity("fr", $created_by, array("Propriete_Location","1"), $lastID, $msg);
		
		return 1;
		
	}
	
	public function Remove($params){
		if(isset($params["id"])){
			
			$data = $this->find('', ['conditions' => [ 'id=' => $params['id'] ] ], '');
			if(count($data) === 1){
				
				$data = $data[0];
				$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
				$msg = ""; // "periode: " . $data["de"] . " / " . $data["a"];

				$this->delete($params["id"]);

				$this->saveActivity("fr", $created_by, ['Propriete_Location', -1], $data["id"], $msg);

				return 1;
				

			}else{
				return 0;
			}

		}else{
			return 0;
		}
	}
	
	public function Create($params){
		$p_l = $this->find('', [ 'conditions'=>['id='=>$params['id']] ], 'v_propriete_location_1');
		
		if(count($p_l)>0){
			$push['Obj']	=	new Propriete_Location;
			$push['PL']	=	$p_l[0];			
		}

		
		$view = new View("propriete_location.create");
		return $view->render($push);
		
	}

	public function Add_Propriete_To_Periode($params = []){
		
		$client = $this->find('', ['conditions'=>['YEAR(created)='=>2021], 'order'=>'first_name'], 'v_contrat');
		$push = [];
		$push['id_propriete']  = $params["id_propriete"];
		if(count($client)>0) $push['clients'] = $client;
		
		$view = new View("propriete_location.propriete_to_periode");
		return $view->render($push);	
	}
	
	public function Get_Periodes_Of_Client($params = []){
		
	}
	
}
$propriete_location = new Propriete_location;