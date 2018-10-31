<?php

include "../src/OTag.php";
$data = json_decode(file_get_contents("generated.json"), TRUE);


$profiles = new OTag('dl',"id='profiles'");
foreach($data as $value){
	$profiles($dt = new OTag('dt',array('id'=>$value['_id'],"class"=>$value['gender'])));
	if(!empty($value['tags'])){
		$dt->__set("class", $dt->__get("class")." ". implode(" ", $value['tags'])); 
	}
	if(isset($value['picture'])){
		$dt($img = new OTag('img',array("src"=>$value['picture'],"class"=>$value['eyeColor'])));
	}
	$dt($check = new OTag('input',['type'=>'checkbox','name'=>'check_'.$value['index'],'checked'=>!empty($value['isActive']), 'disabled']));
	$dt("<label for='check_$value[index]'>$value[name]</label>");
	$profiles($dd = OTag::Craft('dd', $value['greeting'], "class='$value[favoriteFruit]'"));
}

echo "<h1>Profile Test</h1>";
echo "<pre>\n";
echo htmlentities($profiles);
echo "</pre>\n";