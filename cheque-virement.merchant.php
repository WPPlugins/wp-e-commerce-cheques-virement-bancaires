<?php
$nzshpcrt_gateways[$num] = array(
	'name' => 'Paiement par chèque ou virement bancaire',
	'api_version' => 2.0,
	'has_recurring_billing' => true,
	'display_name' => 'Paiement par chèque ou virement bancaire',	
	'wp_admin_cannot_cancel' => false,
	'requirements' => array(),
	'submit_function' => 'submit_cheque_virement',
	'form' => 'form_cheque_virement',
	'internalname' => 'cheque_virement',
	'class_name' => 'wpsc_merchant_cheque_virement',
);

class wpsc_merchant_cheque_virement extends wpsc_merchant {

function submit(){
	global $wpdb;
		$this->set_purchase_processed_by_purchid(2);
	 	$this->go_to_transaction_results($this->cart_data['session_id']);
	}// end of submit
} // end of class


// This function add special message to the transaction result page and report ->
function cheque_virement_custom_message($text) {
			if ($_SESSION['wpsc_previous_selected_gateway']=='cheque_virement')	{
				$text = $text.'				'.get_option('cheque_virement_payment_instructions').'				';
			}
			return $text;
}
add_filter("wpsc_transaction_result_report", "cheque_virement_custom_message");
add_filter("wpsc_transaction_result_message_html", "cheque_virement_custom_message");add_filter("wpsc_transaction_result_message", "cheque_virement_custom_message");


function form_cheque_virement() {
	global $wpdb;
	$output = "<tr>\n\r";
	$output .= "	<td colspan='2'>\n\r";
	
	$output .= "<strong>".__('Enter the payment instructions that you wish to display to your customers when they make a purchase', 'wpsc').":</strong><br />\n\r";
	$output .= "<textarea cols='40' rows='9' name='cheque_virement_payment_instructions'>".stripslashes(get_option('cheque_virement_payment_instructions'))."</textarea><br />\n\r";
	$output .= "<em>".__('For example, this is where you the Shop Owner might enter your bank account details or address so that your customer can make their manual payment.', 'wpsc')."</em>\n\r";
	$output .= "	</td>\n\r";
	$output .= "</tr>\n\r\n\r";
	
	$output.= "<tr><td colspan=2><h4>Paiements en attentes :</h4></td></tr>";
	// Si id d'une vente est passée en GET Mettre a jour une vente dans la bd :
	if (isset($_GET['action_cheque_virement'])){
		if ($_GET['action_cheque_virement']=='recu'){
			$wpdb->query("UPDATE `".WPSC_TABLE_PURCHASE_LOGS."` SET `processed`= '3' WHERE `sessionid`=".$_GET['sessionid']);
		}
	}
	
	// Affiche les ventes par cheque et virement :
	$ventes_cheque_virement=$wpdb->get_results("SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `gateway`='wpsc_merchant_cheque_virement' AND `processed`=2");
	if ($ventes_cheque_virement){
		 foreach ($ventes_cheque_virement as $vente_cheque_virement) {
		 	$output.='<tr><td colspan=2>Vente #'.$vente_cheque_virement->id.' ('.$vente_cheque_virement->totalprice.'€) <a href="http://iamaconcept.com/wp-admin/admin.php?page=wpsc-settings&tab=gateway&action_cheque_virement=recu&sessionid='.$vente_cheque_virement->sessionid.'">Valider la vente (paiement reçu)</a></td></tr>';
		 }
	}
	else{
	$output.='<tr><td colspan=2>Toutes les paiements par chèque et virement bancaires ont été reçus !</td></tr>';	
	}

	return $output;
}

function submit_cheque_virement(){
	if($_POST['cheque_virement_payment_instructions']!=null) {update_option('cheque_virement_payment_instructions',$_POST['cheque_virement_payment_instructions']);}
	return true;
}