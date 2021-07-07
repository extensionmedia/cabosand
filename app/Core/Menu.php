<?php
require_once('Helpers/Modal.php');
require_once('Helpers/Config.php');
require_once('Person.php');

class Menu extends Modal{

	private $columns = array(
		array("column" => "id", "label"=>"#ID"),
		array("column" => "libelle", "label"=>"LIBELLE"),
		array("column" => "url", "label"=>"URL"),
		array("column" => "icon", "label"=>"ICON"),
		array("column" => "parent", "label"=>"PARENT"),
		array("column" => "_order", "label"=>"ORDRE"),
		array("column" => "status", "label"=>"STATUS")
	);
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName("manager_links");
		}catch(Exception $e){
			$this->err->save("Template -> Constructeur","$e->getMessage()");
		}
	}	
	
	public function getColumns(){
		
		if ( isset($this->columns) ){
			return $this->columns;
		}else{
			$columns = array();
			//var_dump($this->getColumnsName("client"));
			foreach($this->getColumnsName("manager_links") as $k=>$v){
				//var_dump($v["Field"]);
				array_push($columns, array("column" => $v["Field"], "label" => $v["Field"]) );
			}
			return $columns;
		}
		
	}
	
	public function editOrder($id, $up_or_down, $id_preview, $id_next, $current_order){
		
		
		
		$this->id = $id;
		$m = $this->read();
		if($up_or_down==="UP"){
			$this->save(array(
				"id"		=>	$id,
				"_order"	=>	$current_order-1
			));
			
			$this->save(array(
				"id"		=>	$id_preview,
				"_order"	=>	$current_order
			));
			
		}else{
			$this->save(array(
				"id"		=>	$id,
				"_order"	=>	$current_order+1
			));
			
			$this->save(array(
				"id"		=>	$id_next,
				"_order"	=>	$current_order
			));
		}
		return 1;
	}
	
	public function drawTable_2(){
		
		$i = 0;
		$m = $this->find(null,array("conditions"=>array("id_parent="=>0), "order"=>"_order"),"v_link");
		$i_count = count($m);		
		
		$returned = '	<div class="col_12">';
		$returned .= '		<ul class="unstyle">';
		
		foreach ($m as $k=>$v){
			$status = ($v["status"])? "<div class='label label-green'>Activé</div>": "<div class='label label-red'>Désactivé</div>";
			$returned .= '		<li>';
			$returned .= '			<a href="#menu" class="__menu">';
			$returned .= '				<div class="icon">' . $v["icon"] . '</div>';
			$returned .= '				<div>'.$v["_order"].' '.$v["libelle"].' '. $status. '</div>';
			$returned .= '				<div class="" style="text-align: right">';
			$returned .= '					<div class="btn-group" style="margin: 0; padding: 0;">';

			if($i > 0){
				$next = ($i === $i_count-1)? 0: $m[$i+1]["id"];
				$returned .= '					<button class="btn up order" data-order="'.$i.'" data-id-n="'.$next.'" data-id-p="'.$m[$i-1]["id"].'" data-id="'.$v["id"].'"><i class="fas fa-chevron-up"></i></button>';
			}
			if($i < $i_count-1){
				$preview = ($i===0)? 0: $m[$i-1]["id"];
				$returned .= '					<button class="btn down order" data-order="'.$i.'" data-id-n="'.$m[$i+1]["id"].'" data-id-p="'.$preview.'" data-id="'.$v["id"].'"><i class="fas fa-chevron-down"></i></button>';
			}
			
			$returned .= "						<button class='btn btn-red remove_ligne' value='".$v["id"] ."' data-page='Menu'  data-id='".$v["id"]."'><i class='fas fa-trash-alt'></i></button>";
			$returned .= "						<button class='btn btn-green _edit_ligne' value='".$v["id"]."' data-page='Menu' data-id='".$v["id"]."'><i class='far fa-edit'></i></button>";
			$returned .= '					</div>';
			$returned .= '				</div>';
			$returned .= '			</a>';
			
			
			$returned .= '			<ul class="unstyle">';
			$data = $this->find(null,array("conditions AND"=>array("id_parent="=>$v["id"]), "order"=>"_order"),null);
			$j = 0;
			$j_count = count($data);
			
			foreach ($data as $kk=>$vv){
				
				$_status = ($vv["status"])? "<div class='label label-green'><i class='fas fa-minus'></i></div>": "<div class='label label-red'><i class='fas fa-minus'></i></div>";
				
				$returned .= '		<li>';
				$returned .= '			<a href="#menu" class="__sub">';
				$returned .= '				<div class="icon">' .$_status. '</div>';
				$returned .= '				<div>'.$vv["libelle"].'</div>';
				$returned .= '				<div class="" style="text-align: right">';
				$returned .= '					<div class="btn-group" style="margin: 0; padding: 0;">';

				if($j > 0){
					$next = ($j === $j_count-1)? 0: $data[$j+1]["id"];
					$returned .= '					<button class="btn up order" data-order="'.$j.'" data-id-n="'.$next.'" data-id-p="'.$data[$j-1]["id"].'" data-id="'.$vv["id"].'"><i class="fas fa-chevron-up"></i></button>';
				}
				if($j < $j_count-1){
					$preview = ($j===0)? 0: $data[$j-1]["id"];
					$returned .= '					<button class="btn down order" data-order="'.$j.'" data-id-n="'.$data[$j+1]["id"].'" data-id-p="'.$preview.'" data-id="'.$vv["id"].'"><i class="fas fa-chevron-down"></i></button>';
				}

				$returned .= "						<button class='btn btn-red remove_ligne' value='".$vv["id"] ."' data-page='Menu'  data-id='".$vv["id"]."'><i class='fas fa-trash-alt'></i></button>";
				$returned .= "						<button class='btn btn-green _edit_ligne' value='".$vv["id"]."' data-page='Menu' data-id='".$vv["id"]."'><i class='far fa-edit'></i></button>";
				$returned .= '					</div>';
				$returned .= '				</div>';


				$returned .= '			</a>';
				$returned .= '		</li>';
				$j++;
			}		



			$returned .= '		</ul>';
			
			
			
			
			$returned .= '		</li>';
			$i++;
		}		
		
		
		
		$returned .= '		</ul>';
		$returned .= '	</div>';
		
		echo $returned;

		
	}
	
	public function Drow(){
		$config = new Config;
		$env = $config->get()["GENERAL"]["ENVIRENMENT"];
		$id_user = $_SESSION[$env]["USER"]["id"];
		
		$links = [
			'Index'					=>	'<li class="open" data-page="index.index"><i class="fas fa-chart-line"></i> Dashboard </li>',
			'Dépense'				=>	'<li class="open" data-page="depense.list"><i class="fas fa-hand-holding-usd"></i> Dépense </li>',
			'Caisse'				=>	'<li class="open" data-page="caisse.list"><i class="fas fa-cash-register"></i> Caisses </li>',
			'Propriété'				=>	'<li class="open" data-page="propriete.list"><i class="fas fa-home"></i> Propriété </li>',
			'Contrat'				=>	'<li class="open" data-page="contrat.list"><i class="fas fa-file-contract"></i> Contrat </li>',
			'Client'				=>	'<li class="open" data-page="client.list"><i class="fas fa-user-tie"></i> Client </li>',
			'Propriétaire'			=>	'<li class="open" data-page="proprietaire.list"><i class="fas fa-user"></i> Propriétaire </li>',
			'Complexe'				=>	'<li class="open" data-page="complexe.list"><i class="fas fa-city"></i> Complexe </li>',
			'Options'				=>	'<li class="has_sub" data-sub-target="options"><i class="fas fa-cog"></i> Options <div class="down"><i class="fas fa-caret-down"></i></div></li>',
			'Général'				=>	'<li class="open sub options hide" data-page="parametres.index"><i class="fas fa-tools"></i> Général </li>',
			'Listes'				=>	'<li class="open sub options hide" data-page="listview.list"><i class="far fa-list-alt"></i> Listes </li>',
			'Log'					=>	'<li class="open sub options hide" data-page="log.list"><i class="fas fa-clipboard-list"></i> Log</li>',
			'Propriété Categorie'	=>	'<li class="open sub options hide" data-page="propriete_category.list"><i class="fas fa-clipboard-list"></i> App Catégorie</li>'
			
		];
			
		$person = new Person;
		$template = '
			<ul>
				{{li}}
			</ul>	
			<div class="" style="background-color:#333; position: absolute; bottom: 0; width: 145px; padding-top: 5px; padding-right: 10px; box-shadow: rgba(0, 0, 0, 0.13) 0px -3px 3px 0px ">
				<ul>';
		if( $person->Is_Permission_Granted(['key'=>'Utilisateur']) )
			$template .= '<li class="open" data-page="person.list"><i class="fas fa-user-friends"></i> Utilisateurs </li>';		
			
			
		$template .= '
					<li><i class="fas fa-question"></i> Support </li>
				</ul>
			</div>
		';
		
		
		
		
		$list = $this->find('', [ 'conditions AND'=>['status='=>1, 'id_parent='=>0], 'order'=>'_order asc' ], '');
		$li = '';
		foreach($links as $k=>$v){
			if( $person->Is_Permission_Granted(['key'=>$k]) || $k === 'Index' )
				$li .= $v;
		}
		return str_replace("{{li}}", $li, $template);
	}
	
	
}
$menu = new Menu;