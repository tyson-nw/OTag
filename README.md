# OTag
A simple tag object that supports nesting and referencable containers.

The goal is to take in the minimum amount of input to create the maximum 
amount of well formatted HTML without having to worry about closing tags 
or accidentally creating infinite loops of objects.

Some Standard syntaxes for createing tags are:
```
<?php
$div = new Tag("div", array("id"=>"main","class"=>"blurb content");
$div->add($head = Tag::Craft("h1", "OTag has these");
$features = array("Object Oriented","nesting","expandable syntax", "prevent infinite reference loops","takes html");
$div->add($ul = new Tag("ul");
$n = 0;
foreach($features as $f){
	$ul->add("<li>$f</li>");
	$n++;
}
$head->add("$n features");
$head->inline();
echo $div;
```
OUTPUTS
```
<div id='main' class='blurb content'>
	<h1>OTag has these 5 features</h1>
	<ul>
		<li>Object Oriented</li>
		<li>nesting</li>
		<li>expandable syntax</li>
		<li>prevent infinite reference loops</li>
		<li>takes html</li>
	</ul>
</div>
```