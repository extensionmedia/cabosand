<?php
require_once('Helpers/Modal.php');
require_once('Helpers/View.php');

class Parametre extends Modal{
	
	private $tableName = 'Manager';
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName(strtolower($this->tableName));
		}catch(Exception $e){
			die($e->getMessage());
		}
	}
	
	public function Get_General($params=[]){
		$manager = $this->fetchAll('manager');
		$push['Obj'] = new Parametre;
		if(count($manager) > 0){
			$push['manager'] = $manager[0];
		}
		$view = new View("parametres.general");
		return $view->render($push);
	}
	
	public function Get_Module($params = []){
		$modules = $this->fetchAll('modules');
		$push['Obj'] = new Parametre;
		$push['modules'] = $modules;
		$view = new View("parametres.module");
		return $view->render($push);	
	}
	
	public function Get_Modules_Only(){
		$modules = $this->fetchAll('modules');
		$template = '';
		foreach($modules as $k=>$v){
			$selected = $k===0? 'selected': '';
			$template .= '
				<div data-id="'.$v["id"].'" class="item '.$selected.' mdl d-flex space-between" style="border-bottom: 1px solid rgba(232,232,232,1.00); font-size: 12px;">
					<div class="d-flex">
						<div class="p-10 id" style="width: 50px">'.$v["id"].'</div>
						<div class="p-10 name">'.$v["module_name"].'</div>						
					</div>
					<div style="padding-top:2px">
						<button class="transparent edit"> <i class="fas fa-ellipsis-h"></i> </button>
					</div>
				</div>
			';
		}
		return $template;
	}
	
	public function Get_Module_Actions($params = []){
		$id_module = isset($params['id_module'])? $params['id_module']: 0;
		$actions = $this->find('', ['conditions'=>['id_module='=>$id_module] ], 'module_actions');
		$items = '				
				<div class="item d-flex space-between" style="background-color: rgba(232,232,232,1.00); font-size: 12px; border-top-right-radius: 5px; border-top-left-radius: 5px">
					<div class="d-flex">
						<div class="p-10" style="width: 50px">ID</div>
						<div class="p-10">ACTION</div>					
					</div>
					<div>
						<button class="actions_add"><i class="fas fa-plus"></i></button>
						<button value="'.$id_module.'" class="actions_refresh hide"></button>
					</div>
				</div>';
		foreach($actions as $k=>$v){
			$items .= '
				<div class="item actions d-flex space-between" style="border-bottom: 1px solid rgba(232,232,232,1.00); font-size: 12px;">
					<div class="d-flex">
						<div class="p-10 id" style="width: 50px">'.$v["id"].'</div>
						<div class="p-10 name">'.$v["module_action"].'</div>						
					</div>
					<div style="padding-top:2px">
						<button class="transparent edit"> <i class="fas fa-ellipsis-h"></i> </button>
					</div>
				</div>
			';			
		}
		return $items;

		
	}
	
}
$parametre = new Parametre;