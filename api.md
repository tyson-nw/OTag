#OTag Methods#

##Constants##
* __DISPLAY_BLOCK__ - Display the contents of this tag as a block.
* __DISPLAY_INLINE__ - Wrap the tags around the contents of the tag, do not indent contents and remove newlines.
* __DISPLAY_UNPAIRED__ - Display this tag as an unpaired html tag.  Otherwise the tag will be paired and filled with. 

##Public Functions##

###OTag::__construct($tag,$attributes,$display)##
Returns a OTag object with the tag set to $tag, the tag attibutes to the contents
of $attributes.  $attributes can be an associate array, if a non associate key=>value 
pair is given the attribute will be treated as a boolean attribute.  If $attributes is
a string it will be parsed then rebuilt for final output.  If it is not valid HTML 
attributes then an notice or exception will be thrown. 

###OTag::Craft($tag,$contents,$attributes,$display)##
Exactly the same as $tag = OTag::__construct(); $tag->add($contents);. 

###$tag($content)##
Alias of ->add()

###$tag->add($content)##
Adds $content to $tag.  Can take an array of strings, OTags, or object with __toString(). 

###$tag->addClass($class)##
Appends contents of class to class attribute

###$tag->__toString()##
Returns the HTML of the OTag and all it's contents.

###$tag->__isset($attribute)##
Returns TRUE if the attribute is set. FALSE if not

###$tag->__get($attribute)##
Returns the value if the attribute $name if set.  Otherwise null. 

###$tag->__set($attribute, $value)##
Overwrites the current value of $attribute.  If you want to append to the class attribute use addClass($class).  

###$tag->__clone()###
Walk the contents and return a duplicate copy of the OTag and it's contents.  If a content object is un-cloneable, run __toString and add as string content.

##Inrernal Functions##
###_registerParent(this)
OTag passes itself to the OTag just added, child OTag then registers the parent.  If it is added as content to another OTag, throws Exception.