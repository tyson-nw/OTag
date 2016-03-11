<?php
namespace OTag;

class OTable extends OTagObject{
	private $thead;
	private $tbody;
	private $tfoot;
	public function __construct($attributes){
		$this->tag = "table";
		$this->display = self::BLOCK;
		if(is_string($attributes)){
			$this->attributes = self::_parse_attributes($attributes);
		}elseif(is_array($attributes)){
			$this->attributes = $attributes;
		}elseif(empty($attributes)){
			$this->attributes = array();
		}
	}
	
	public function addHead($cols,$attibutes = array()){
		if(empty($this->thead)){
			$thead = new OTag("thead");
		}
		if(isString($cols)){
			//test for csv
				//wrap csv in th
			readcsv($cols);
			//otherwise add to 
		}elseif($cols instanceof OTagObject){
			$this->thead->add($cols);
		}elseif(is_array($cols)){
			$this->thead->add($tr = new OTag("tr", $attributes));
			foreach($cols as $th){
				$tr->add($th);
			}
		}
	}
}