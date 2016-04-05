<?php
error_reporting(E_ALL);
/**
 * These are example uses for OTag
**/ 
include "OTag.php";
OTag::$indent_char = "  ";
OTag::$indent = -1;
$body = new OTag("body");
$body("<h1>OTag example page</h1>");
$body(OTag::Craft("p", "Welcome to the demo of OTag, the HTML Generating Tag Object.  
OTag strives to be a lightweight html generator that is with some terse code you can spin
out layered html of any complexity without missing a close tag or malforming your html.
It strives to give you maximum flexibility while staying out of your way as much as possible.",array("style"=>"width: 20em;")));

$body("<h2>Strings only, no fancy</h2>");
$body("<p>add content with no details, you control the formatting</p>");

$body("<h2>Inline content</h2>");
$body($p = new OTag("p",array("id"=>"inline_test"),OTag::DISPLAY_INLINE));
$p("OTags with the \$display flagged as");
$p(OTag::Craft("strong","OTag::INLINE"));
$p("will include the contents inline with itself, removing newlines and indentation.");



$body(OTag::Craft("h2", "Crafted with attribute strings", "class='header'"));
$body(OTag::Craft("p", "This is an italicized string", "style='font-style: italic;'"));

$csv = <<<CSV
ID,user,joined,code
253,tempor.arcu@justoProin.com,2014-02-13 17:59:48,4
254,ac.mattis@sollicitudinadipiscingligula.net,2014-11-16 18:45:24,2
255,ante@disparturient.ca,2013-07-28 00:24:27,3
256,molestie@Vivamusnibh.ca,2014-05-21 06:31:10,3
257,Fusce.mi@velpedeblandit.ca,2016-04-23 06:47:26,4
258,dictum@dolorvitaedolor.ca,2012-12-07 12:41:34,1
259,nulla.ante.iaculis@est.net,2012-12-19 03:39:58,3
260,enim.Curabitur.massa@lorem.edu,2013-04-30 00:56:20,1
261,a.nunc@Duiselementumdui.ca,2013-09-07 16:30:12,3
262,elit@Maecenasiaculisaliquet.co.uk,2014-04-09 02:56:48,1
263,et@Aeneanmassa.net,2014-10-19 08:32:11,1
264,felis.eget.varius@Maecenasmalesuadafringilla.edu,2015-01-22 18:51:57,4
265,Pellentesque.tincidunt.tempus@lectus.co.uk,2016-01-30 21:49:13,4
266,blandit.congue@nullaante.ca,2016-03-27 14:42:04,3
CSV;
$data = explode("\n",$csv);
$body(OTag::Craft("h2","Generate from csv, filter to different tables"));
$tbodies = array();
$containers = array();
$head = "<thead><tr><th>".implode("</th><th>", str_getcsv(array_shift($data)))."</th></tr></thead>";
foreach($data as $raw){
	$row = str_getcsv($raw);
	if(!isset($containers[$row[3]])){
		$containers[$row[3]] = new OTag();
		$containers[$row[3]]("<h3>Code $row[3]</h3>");
		$containers[$row[3]]($table = OTag::Craft("table",$head));
		$table($tbodies[$row[3]] = new OTag("tbody", "id='code$row[3]'"));
	}
	$tbodies[$row[3]]($curr = OTag::Craft("tr", "<td>".implode("</td><td>",$row)."</td>"));
	if(strtotime($row[2])> time()){
		$curr->style="font-style: italic;";
	}
}
ksort($containers);
foreach($containers as $container){
	$body($container);
}

$body("<h2>Paired and unpaired empty Tags</h2>");
$body("<p>By default a tag with no content will be considered an upaired tag.  Though you can explicity set an OTag.</p>");
$body($paired = new OTag("div"));
$p = new OTag("p");
$p->empty = OTag::PAIRED;
$paired("Force Paired tag: ");
$paired(htmlentities($p->__toString()));
$body($default = new OTag("div"));
$d = new OTag("img", array("src"=>"http://example.com/aaa.png"));
$default("Default Behavior: ");
$default(htmlentities($d));
$body($force = new OTag("div"));
$f = new OTag("input", array("type"=>"submit"));
$f->empty = OTag::DISPLAY_UNPAIRED;
$force("Forced unpaired tag: ");
$force(htmlentities($f));

$body("<h2>Change attribute quotes</h2>");
$body("<p>Attribute quote character can be set globally for all OTag's before outputting to string</p>");
$body($default = new OTag("div",null,OTag::DISPLAY_INLINE));
$default("Default Behavior:");
$input = new OTag("input","id='test' name='test_in' type='text' value='Let\'s test this'");
$default(htmlentities($input->__toString()));
OTag::$quote = "\"";
$body($quote = new OTag("div",null,OTag::DISPLAY_INLINE));
$quote("Updated OTag::\$quote:");
$quote(htmlentities($input->__toString()));
echo $body;