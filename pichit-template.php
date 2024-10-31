<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once ('pichit.inc.php');
$token = get_option( 'pichit_token' )['token'];

$ost = new pichit();
$getjson = $ost->get_json_response('pichit.me/api/contributions/?page_nr=1',$token);
$array = $ost->get_array_of_json($getjson);
//print_r($array);
		$html = '<div class="media-toolbar"><div class="media-toolbar-primary"><input type="search" placeholder="Search" class="search"><a class="pichit-search">Find</a></div></div><ul class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-280">';
		foreach($array as $arr) {
			$html .= '<li class="attachment save-ready"><div class="attachment-preview type-image subtype-jpeg landscape"><div class="thumbnail"><div class="centered"><img style="width: auto;height: auto;" src="' . $arr->photos[0]->url . '" /></div></div><a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a></div></li>';
		}
		$html .= '<a href="#" style="" class="load-standard tjena"><img src="/wp-content/plugins/pichit/white-pichit-logo.png" />Load more...</a></ul></div></div>';
		echo $html;
?>