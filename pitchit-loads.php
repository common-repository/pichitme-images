<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once ('pichit.inc.php');
$token = get_option( 'pichit_token' )['token'];

if(isset($_POST['load'])){
	$current_length = $_POST['load'];
	$new_length = ($current_length/20)+1;
	$ost = new pichit();
	$getjson = $ost->get_json_response('pichit.me/api/contributions/?page_nr='.$new_length,$token);
	$array = $ost->get_array_of_json($getjson);
	foreach($array as $arr) {
		$html .= '<li class="attachment save-ready"><div class="attachment-preview type-image subtype-jpeg landscape"><div class="thumbnail"><div class="centered"><img style="width: auto;height: auto;" src="' . $arr->photos[0]->url . '" /></div></div><a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a></div></li>';
	}
	echo $html;
}


?>