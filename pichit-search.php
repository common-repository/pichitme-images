<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once ('pichit.inc.php');
$token = get_option( 'pichit_token' )['token'];

if(isset($_POST['value'])){
	$value = $_POST['value'];
	$ost = new pichit();
	$getjson = $ost->get_json_response('pichit.me/api/contributions/?q='.$value,$token);
	$array = $ost->get_array_of_json($getjson);
	
	foreach($array as $arr) {
		$html .= '<li class="attachment save-ready"><div class="attachment-preview type-image subtype-jpeg landscape"><div class="thumbnail"><div class="centered"><img style="width: auto;height: auto;" src="' . $arr->photos[0]->url . '" /></div></div><a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a></div></li>';
	}
	$html .= '<a href="#" style="" data-keyword="'.$value.'" class="load-search tjena"><img src="/wp-content/plugins/pichit/white-pichit-logo.png" />Load more...</a></ul></div></div>';
	echo $html;
}

?>