#OTag Methods#

##DISPLAY_BLOCK##
Display the contents of this tag as a block

##DISPLAY_INLINE##
Wrap the tags around the contents of the tag, do not indent contents and remove newlines.

##DISPLAY_UNPAIRED##
Display this tag as an unpaired html tag.  Otherwise the tag will be paired and filled with 

##OTag::__construct($tag,$attributes,$display)##
Returns a OTag object with the tag set to $tag, the tag attibutes to the contents
of $attributes.  $attributes can be an associate array, if a non associate key=>value 
pair is given the attribute will be treated as a boolean attribute.  If $attributes is
a string it will be parsed then rebuilt for final output.  If it is not valid HTML 
attributes then an notice or exception will be thrown. 

##OTag::Craft($tag,$contents,$attributes,$display)##

$tag($content)

$tag->add($content)

$tag->addClass($class)

$tag->__toString()

$tag->__get($name)

$tag->__set($name, $value)