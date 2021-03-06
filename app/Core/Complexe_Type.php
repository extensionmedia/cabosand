<?php
require_once('Helpers/Modal.php');

class Complexe_Type extends Modal{

	private $columns = array(
		array("column" => "id", "label"=>"#ID", "width"=>50),
		array("column" => "complexe_type", "label"=>"Complexe Type")
	);
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName("complexe_type");
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
			foreach($this->getColumnsName("complexe_type") as $k=>$v){
				//var_dump($v["Field"]);
				array_push($columns, array("column" => $v["Field"], "label" => $v["Field"]) );
			}
			return $columns;
		}
		
	}
}
$complexe_type = new Complexe_Type;