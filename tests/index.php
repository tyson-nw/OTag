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
		$dt($img = new OTag('img',array("src"=>$value['picture'],"class"=>$value['eyeColor']),OTag::DISPLAY_UNPAIRED));	//DISPLAY_UNPAIRED
	}

	$dt->add($check = new OTag('input',['type'=>'checkbox','id'=>'check_'.$value['index'],'name'=>'check['.$value['index']."]",'checked'=>$value['isActive'], 'disabled']),OTag::DISPLAY_UNPAIRED);
	$dt->add(OTag::Craft("label", $value['name'], ['for'=> 'check_' . $value['index']], OTag::DISPLAY_INLINE));
	//$dt->add(...) changed to $dt(...) because __invoke function was added
	$dt($check = new OTag('input',['type'=>'checkbox','name'=>'check_'.$value['index'],'checked'=>$value['isActive'], 'disabled']));
	$dt(OTag::Craft("label", ['for'=>'check_'.$value['index']],OTag::DISPLAY_INLINE));
	$profiles($dd = OTag::Craft('dd', $value['greeting'], "class='$value[favoriteFruit]'"));
}

echo "<h1>Profile Test</h1>";
echo "<pre>\n";
echo htmlentities($profiles->__toString());	//changed from toString() to __toString()
echo "</pre>\n";