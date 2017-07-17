<?php

/*
Plugin Name:WP e-Commerce Chèque Virement Bancaire
Plugin URI: http://wpcb.fr/cheque-virement
Description: Plugin de paiement par chèque et virement bancaire (Plugin requis : WP e-Commerce)
Version: 1.0.4
Author: 6WWW
Author URI: http://6www.net
*/



if (!class_exists('cheque_virement_Loader'))
{
	class cheque_virement_Loader {

		function load()
		{

			// Init options & tables during activation & deregister init option
			register_activation_hook( __file__, array(&$this, 'activate' ));
			register_deactivation_hook( __file__, array(&$this, 'deactivate' ));
			if(get_option('cheque_virement_msg'))
			{
				add_action('admin_notices', $this->echo_error());
				delete_option('cheque_virement_msg');
			}
		}

		function echo_error()
		{
			echo '<div id="message" class="error"><p>' . get_option('cheque_virement_msg') . '</p></div>';
		}

		/*
			Activate the plugin
		*/
		function activate()
		{
			$cheque_virement_Dir = dirname(__file__);
			if(get_option('wpsc_version')){
				if(floatval(get_option('wpsc_version'))>3.7){
					$pluginDir = dirname(dirname(__file__)) . '/wp-e-commerce/wpsc-merchants';
				} else {
					$pluginDir = dirname(dirname(__file__)) . '/wp-e-commerce/merchants';
				}
			} else {
				$pluginDir = dirname(dirname(__file__)) . '/wp-e-commerce/merchants';
			}
			$sourceFile = $cheque_virement_Dir . '/cheque-virement.merchant.php';
			$destinationFile = $pluginDir . '/cheque-virement.merchant.php';

			// Copy the file to the WP e-Commerce merchants folder
			if(file_exists($pluginDir))
			{
				@copy($sourceFile, $destinationFile);
					if(!file_exists($destinationFile))
					{
						if(get_option('cheque_virement_msg'))
						{
							update_option('cheque_virement_msg', '<strong>WP e-Commerce cheque virement :</strong> Please copy cheque-virement.merchant.php manually to wp-e-commerce/merchants.');
						} else {
							add_option('cheque_virement_msg', '<strong>WP e-Commerce cheque virement :</strong> Please copy cheque-virement.merchant.php manually to wp-e-commerce/merchants.');
						}
					}					else					{					// Set default values :					update_option('cheque_virement_payment_instructions','Vous avez choisi un paiement par chèque ou virement bancaire. Pour finaliser votre achat :Envoyez votre chèque à l\'adresse ... à l\'ordre ...Envoyez votre virement à ce RIB : XXX-XXXXX-Merci'); 					}
			} else {
				if(get_option('cheque_virement_msg'))
				{
					update_option('cheque_virement_msg', "WP e-Commerce wasn't found, please install it first.");
				} else {
					add_option('cheque_virement_msg', "WP e-Commerce wasn't found, please install it first.");
				}
			}
		}

		/*
			Deactivate the plugin
		*/
		function deactivate()
		{
			$wpsc_plugin_dir = dirname(dirname(__file__)) . '/wp-e-commerce/merchants';
			if(file_exists($wpsc_plugin_dir . '/cheque-virement.merchant.php'))
			{
				unlink($wpsc_plugin_dir.'/cheque-virement.merchant.php');
			}
			
		}
	}

	$cheque_virement_Loader = new cheque_virement_Loader();
	$cheque_virement_Loader->load();

}
?>