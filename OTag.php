<?php
/**
 *
**/
class OTag{
	const FORMAT = "<%s%s>%s</%s>";
	const PAIRED = "&nbsp";
	const UNPAIRED = NULL;
	const EMPTY_FORMAT = "<%s%s />";
	const BLOCK = FALSE;
	const INLINE = TRUE;
	public static $quote = "'";
	public static $indent = 0;
	public static $indent_char = "\t";
	public static $nl_char = "\n";
	private static $outputting = FALSE;
	
	public $tag;
	public $empty = NULL;
	private $attributes;
	private $contents = array();
	private $parents = array();
	private $display;
	
	public static function Craft($tag=NULL, $content="", $attributes=array(), $display=self::BLOCK){
		$new = new OTag($tag, $attributes, $display);
		$new->add($content);
		return $new;
	}
	
	public function __construct($tag=NULL, $attributes=array(), $display=self::BLOCK){
		$this->tag = $tag;
		$this->display = $display;
		if(is_string($attributes)){
			$this->attributes = self::_parse_attributes($attributes);
		}elseif(is_array($attributes)){
			$this->attributes = $attributes;
		}elseif(empty($attributes)){
			$this->attributes = array();
		}
	}
	
	public function add($content){
		if($content instanceof OTag){
			$content->_registerParent($this);
			$this->_checkloop($content);
			$this->contents[] = $content;
		}else{
			$this->contents[] = $content;
		}
		return $content;
	}
	
	public function __set($attribute, $content){
		if($content===TRUE){
			$this->attributes[]=$attribute;
		}elseif($content==FALSE && ($pos = array_search($attribute, $this->attributes))!== FALSE){
			unset($this->attributes[$pos]);
		}else{
			$this->attributes[$attribute]=$content;
		}
	}
	
	public function __get($attribute){
		if(isset($this->attributes[$attribute])){
			return $this->attributes[$attribute];
		}
		return NULL;
	}
	
	public function __toString(){
		reset($this->contents);
		$first = FALSE;
		if(self::$outputting){
			self::$indent++;
		}else{
			$first = self::$outputting = TRUE;
		}
		
		$attribs = "";
		$attrib_format = " %s=".self::$quote."%s".self::$quote;
		foreach($this->attributes as $key=>$value){
			if(is_numeric($key)){
				$attribs .= " " . $value;
			}else{
				$attribs .= sprintf($attrib_format, $key, $value);
			}
		}
		
		$args = array($this->tag, $attribs, "", $this->tag);
		
		$nl = self::$nl_char;
		$t = self::$indent_char;
		if($this->display == self::INLINE){
			self::$nl_char = " ";
			self::$indent_char = "";
		}
		
		if(count($this->contents)==0 && $this->empty == self::PAIRED){
			$this->contents[] = $this->empty;
		}
		
		
		if(empty($this->tag)){
//if an empty tag, no tag wrtappers or attributes
			$out = "";
			foreach($this->contents as $content){
				if($content instanceof OTag){
					self::$indent--;
					$out .= self::_nl(self::$indent+1).$content->__toString();
					self::$indent++;
				}else{
					$out .= self::_nl(self::$indent).$content;
				}
			}
		}elseif(count($this->contents)==0){
//if an empty tag
			$out = self::_nl(self::$indent) . vsprintf(self::EMPTY_FORMAT,array($this->tag,$attribs));
		}elseif(count($this->contents) == 1 && !(current($this->contents) instanceof OTag)){
//if contains a single item and contents not an OTag, don't nl indent contents'
			$args[2] = current($this->contents);
			$out = vsprintf(self::FORMAT, $args);
		}else{
//if contains multiple contents or content is a OTag
			$args[2] = "";
			foreach($this->contents as $content){
				if($content instanceof OTag){
					$args[2] .= self::_nl(self::$indent+1).$content->__toString();
				}else{
					$args[2] .= self::_nl(self::$indent+1).$content;
				}
			}
			
			$args[2] .= self::_nl(self::$indent);
			if($this->display == self::INLINE){
				$args[2]=trim($args[2]);
			}
			$out = 	vsprintf(self::FORMAT, $args);
		}
		if($this->display == self::INLINE){
			self::$nl_char = $nl;
			self::$indent_char = $t;
		}
		
		if($first){
			self::$outputting = FALSE;
		}else{
			self::$indent--;
		}
		return $out;
	}
	
	private static function _nl($count,$nl=NULL,$i=NULL){
		if($count<0){$count=0;}
		if(empty($nl)){$nl = self::$nl_char;}
		if(empty($i)){$i = self::$indent_char;}
		return $nl.str_repeat($i,$count);
	}
	
	private function _registerParent($parent){
		if(count($this->parents)){
			trigger_error("Tag '" . $this->tag . "' has been added to second parent '" . $parent->tag ."'");
		}
		$this->parents[] = $parent;
	}
	
	private function _checkLoop($child){
		if($child===$this){
			throw new Exception("A Tag has been made it's own parent");
		}
		foreach($this->parents as $parent){
			$parent->_checkLoop($child);
		}
	}
	
/*
*http://stackoverflow.com/questions/30199001/how-can-i-parse-an-attribute-string-to-an-array-in-php
*/
	private static function _parse_attributes($input){
		$re = "/(?:\\s*(\\w+)\\s*=\\s*(?:'((?:[^'\\\\]|\\\\')*)'|\"((?:[^\"\\\\]|\\\\\")*)\"?|(\\w+)))/"; 

		preg_match_all($re, $input, $parts, PREG_SET_ORDER);
		
		$result=array();
		
		foreach ($parts as $part) {
		    $result[$part[1]] = stripslashes($part[2] ? $part[2] : ($part[3] ? $part[3] : $part[4]));
			if($result[$part[1]] == $part[1]){	//added to trim down boolean attributes. Issue #3
				unset($result[$part[1]]);
				$result[] = $part[1];
			}
		}
		return $result;
	}
}