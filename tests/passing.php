<?php

include "../OTag.php";
$data = json_decode(file_get_contents("generated.json"), TRUE);


$profiles = new OTag('dl',"id='profiles'");
foreach($data as $value){
	$profiles->add($dt = new OTag('dt',array('id'=>$value['_id'],"class"=>$value['gender'])));
	if(!empty($value['tags'])){
		$dt->__set("class", $dt->__get("class")." ". implode(" ", $value['tags'])); 
	}
	if(isset($value['picture'])){
		$dt->add($img = new OTag('img',array("src"=>$value['picture'],"class"=>['eyecolor'])));
	}
	$dt->add($check = new OTag('input',['type'=>'checkbox','name'=>'check_'.$value['index'],'checked'=>$value['isActive'], 'disabled']));
	$dt->add("<label for='check_$value[index]'>$value[name]</label>");
	$profiles->add($dd = OTag::Craft('dd', $value['greeting'], "class='$value[favoriteFruit]'"));
}

echo "<h1>Profile Test</h1>";
echo "<pre>\n";
echo htmlentities($profiles);
echo "</pre>\n";