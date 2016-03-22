<?php

include "../OTag.php";
$data = json_decode(file_get_contents("generated.json"), TRUE);


$profiles = new OTag('dl',"id='profiles'");
foreach($data as $value){
	$profiles($dt = new OTag('dt',array('id'=>$value['_id'],"class"=>$value['gender'])));
	if(!empty($value['tags'])){
		$dt->addClass(implode(" ", $value['tags'])); 
	}
	if(isset($value['picture'])){
		$dt($img = new OTag('img',array("src"=>$value['picture'],"class"=>['eyecolor']."_eye"),OTag::DISPLAY_UNPAIRED));
	}
	$dt->add($check = new OTag('input',['type'=>'checkbox','id'=>'check_'.$value['index'],'name'=>'check['.$value['index']."]",'checked'=>$value['isActive'], 'disabled']),OTag::DISPLAY_UNPAIRED);
	$dt->add(OTag::Craft("label", $value['name'], ['for'=> 'check_' . $value['index']], OTag::DISPLAY_INLINE));
	$profiles($dd = OTag::Craft('dd', $value['greeting'], "class='$value[favoriteFruit]'"));
}

echo "<h1>Profile Test</h1>\n";
echo "<pre>\n";
echo htmlentities($profiles->toString());
echo "</pre>\n";