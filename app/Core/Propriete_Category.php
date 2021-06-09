<?php
require_once('Helpers/Modal.php');

class Propriete_Category extends Modal{

	private $columns_ = array(
		array("column" => "id", "label"=>"#ID", "width"=>50),
		array("column" => "propriete_category", "label"=>"Catégorie de Propriété"),
		array("column" => "is_default", "label"=>"PAR DEFAUT", "width"=>90)
	);
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName("propriete_category");
		}catch(Exception $e){
			$this->err->save("Template -> Constructeur","$e->getMessage()");
		}
	}	
	
		
	public function getColumns(){
		
		if ( isset($this->columns) ){
			return $this->columns;
		}else{
			$columns = array();
			$l = new ListView();
			foreach($l->getDefaultStyle("propriete_category")["data"] as $k=>$v){
				array_push($columns, array("column" => $v["column"], "label" => $v["label"], "style"=>$v["style"], "display"=>$v["display"], "format"=>$v["format"]) );
				
			}
			array_push($columns, array("column" => "actions", "label" => "", "style"=>"min-width:105px; width:105px", "display"=>1) );
			return $columns;
		}
		
	}
}
$propriete_category = new Propriete_Category;