<?php
/**
 *	Quick generic HTML generating object.
 *
 * @author		Tyson Vanover
 * @author		Daniel J. Arredondo <djarredon@gmail.com>
 * @copyright	Copyright (c) 2015 Tyson of the Northwest
 * @license    https://github.com/tyson-nw/OTag/blob/master/LICENSE MIT License
 * @link       https://github.com/tyson-nw/OTag
 **/
class OTag extends OTagObject{
	/**
	 * Appends to the end of the "class" string in $attribute
	 * with either a String or an
	 * @param string $string	The string to append to the list of attributes
	 * @return void		return value not used
	 * @access	public
	 **/
	public function addClass($string) {
		/*
		 * If there is a previously existing "class" attribute then $string is
		 * appended to that string, otherwise attribute['class'] is set to
		 * the incoming string
		 */
		if (isset($attribute['class']))
			$attribute['class'] .= " ".$string;
		else
			$attribute['class'] = $string;
	}

	/**
	 * returns new instance of OTag given data to copy
	 * @param object $tag	the tag to add
	 * @param string $content the content to add
	 * @param array $attributes	the attributes to add
	 * @param boolean $display=self::DISPLAY_BLOCK  Whether to display the block of text
	 * @return object	the OTag created with the given data
	 * @access	public
	 * @static
	 **/
	public static function Craft($tag=NULL, $content="", $attributes=array(), $display=self::DISPLAY_BLOCK){
		/*
		 * check if content is an array instead of a string
		 * iteratively add content to $new
		 */
		$new = new OTag($tag, $attributes, $display);
		if (is_array($content)) {
			foreach($content as $var)
				$new($var);
		}
		else
			$new($content);
		return $new;
	}

	/**
	 * default constructor
	 * @param object $tag	the tag to set
	 * @param mixed $attributes	the attributes to set
	 * 				This can be either a string or an array
	 * @param boolean $display=self::DISPLAY_BLOCK  Whether to display the block of text
	 * @access	public
	 **/
	public function __construct($tag=NULL, $attributes=array(), $display=self::DISPLAY_BLOCK){
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
}

/**
 * Class OTagObject
 */
abstract class OTagObject{
	/**
	 *
     */
	const FORMAT = "<%s%s>%s</%s>";
	/**
	 *
     */
	const PAIRED = "&nbsp";
	/**
	 * boolean. True if tag is <br /> or other tag with no paired closing
     */
	const DISPLAY_UNPAIRED = NULL;
	/**
	 *
     */
	const EMPTY_FORMAT = "<%s%s />";
	/**
	 *
     */
	const DISPLAY_BLOCK = FALSE;
	/**
	 *
     */
	const DISPLAY_INLINE = TRUE;
	/**
	 * @var string
     */
	public static $quote = "'";
	/**
	 * number of spaces to indent
	 * @var int
     */
	public static $indent = 0;
	/**
	 * indent character
	 * @var string
     */
	public static $indent_char = "\t";
	/**
	 * new line character
	 * @var string
     */
	public static $nl_char = "\n";
	/**
	 * @var bool
     */
	protected static $outputting = FALSE;

	/**
	 * <div>/<ul><p>/(HTML tag), ex: "p", "h", "h2"
	 * @var
     */
	public $tag;
	/**
	 * @var null
     */
	public $empty = NULL;
	/**
	 * HTML attributes, ex: array("style"=>"width: 20em;"),"class='header'",
	 * 								"style='font-style: italic;'"
	 * @var
     */
	protected $attributes;
	/**
	 * Usually a block of text. Ex: a paragraph
	 * @var array
     */
	protected $contents = array();
	/**
	 * @var array
     */
	protected $parents = array();
	/**
	 * @var
     */
	protected $display;	

	/**
	 * OTagObject constructor.
     */
	abstract public function __construct();

	/**
	 * @param $content 
	 * @return mixed
	 * @throws Exception
	 * @access public
     */
	public function __invoke($content)
	{
		if($content instanceof OTagObject){
			$content->_registerParent($this);
			$this->_checkLoop($content);
			$this->contents[] = $content;
		}else{
			$this->contents[] = $content;
		}
		return $content;
	}
	
	/**
	 * sets $attribute and $content variables
	 * @param mixed $attribute the attributes to be set
	 * @param array $content the array of contents to be set
	 * @access public
	 */
	public function __set($attribute, $content){
		if($content===TRUE){
			$this->attributes[]=$attribute;
		}elseif($content==FALSE && ($pos = array_search($attribute, $this->attributes))!== FALSE){
			unset($this->attributes[$pos]);
		}else{
			$this->attributes[$attribute]=$content;
		}
	}

	/**
	 * @param mixed $attribute
	 * @access public
	 * @return null
     */
	public function __get($attribute){
		if(isset($this->attributes[$attribute])){
			return $this->attributes[$attribute];
		}
		return NULL;
	}

	/**
	 * Generates HTML out of the data given
	 * @access public
	 * @return string	The data in HTML format
     */
	public function __toString(){
		reset($this->contents);
		$first = FALSE;		//
		if(self::$outputting){
			self::$indent++;
		}else{
			$first = self::$outputting = TRUE;
		}

		$attribs = "";
		$attrib_format = " %s=".self::$quote."%s".self::$quote;
		foreach($this->attributes as $key=>$value){
			if($value === true) {
				$attribs .= " " . $key;
			}
			else if ($value === false) {
			}
			else if(is_numeric($key)){
				$attribs .= " " . $value;
			}else{
				$attribs .= sprintf($attrib_format, $key, $value);
			}
		}

		$args = array($this->tag, $attribs, "", $this->tag);

		$nl = self::$nl_char;
		$t = self::$indent_char;
		if($this->display == self::DISPLAY_INLINE){
			self::$nl_char = " ";
			self::$indent_char = "";
		}

		if(count($this->contents)==0 && $this->empty == self::PAIRED){
			$this->contents[] = $this->empty;
		}


		$out = "";
		if(empty($this->tag)){
			//if an empty tag, no tag wrtappers or attributes
			//$out = "";
			foreach($this->contents as $content){
				if($content instanceof OTagObject){
					self::$indent--;
					$out .= self::_nl(self::$indent+1).$content->__toString();
					self::$indent++;
				}else{
					$out .= self::_nl(self::$indent).$content;
				}
			}
		}elseif(count($this->contents)==0){
			//if an empty tag
			$out = vsprintf(self::EMPTY_FORMAT,array($this->tag,$attribs));
		}elseif(count($this->contents) == 1 && !(current($this->contents) instanceof OTagObject)){
			//if contains a single item and contents not an OTag, don't nl indent contents'
			$args[2] = current($this->contents);
			$out = vsprintf(self::FORMAT, $args);
		}else{
			//if contains multiple contents or content is a OTag
			$args[2] = "";
			foreach($this->contents as $content){
				if($content instanceof OTagObject){
					$args[2] .= self::_nl(self::$indent+1).$content->__toString();
				}else{
					$args[2] .= self::_nl(self::$indent+1).$content;
				}
			}

			$args[2] .= self::_nl(self::$indent);
			if($this->display == self::DISPLAY_INLINE){
				$args[2]=trim($args[2]);
			}
			$out = 	vsprintf(self::FORMAT, $args);
		}
		if($this->display == self::DISPLAY_INLINE){
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

	//TODO: anonamyze the function and roll into __tostring.
	/**
	 * @param int 		$count	description
	 * @param string 	$nl		newline character
	 * @param string 	$i		indent character
	 * @return string	new line character with $count number of indent characters appended
     */
	private static function _nl($count, $nl=NULL, $i=NULL){
		if($count<0){$count=0;}
		if(empty($nl)){$nl = self::$nl_char;}
		if(empty($i)){$i = self::$indent_char;}
		return $nl.str_repeat($i,$count);
	}

	/**
	 * @param object $parent
     */
	protected function _registerParent($parent){
		if(count($this->parents)){
			trigger_error("Tag '" . $this->tag . "' has been added to second parent '" . $parent->tag ."'");
		}
		$this->parents[] = $parent;
	}

	/**
	 * @param object $child
	 * @throws Exception
     */
	protected function _checkLoop($child){
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
	/**
	 * @param string $input
	 * @return array
     */
	protected static function _parse_attributes($input){
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