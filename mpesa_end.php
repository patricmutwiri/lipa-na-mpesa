<?php
/**
 * @package	Lipa na Mpesa for HikaShop for Joomla!
 * @version	1.0
 * @author	patrick
 * @copyright	(C) 2010-2015 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="hikashop_mpesa_end" id="hikashop_mpesa_end">
	<?php 
		$app = JFactory::getApplication();
		$input = $app->input;
		$mpesa_code = $input->get('mpesa_code');
		if(empty($mpesa_code)) {	?>
	<span id="hikashop_mpesa_end_message" class="hikashop_mpesa_end_message">
		<?php echo JText::sprintf('PLEASE_WAIT_BEFORE_REDIRECTION_TO_X',$this->payment_name).'<br/>'. JText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED');?> <!-- Waiting message -->
	</span>
	<!-- To send all requiered information, a form is used. Hidden input are setted with all variables, and the form is auto submit with a POST method to the payment plateform URL -->
	<?php 
		//$uri = JFactory::getURI();
		//$absolute_url = $uri->toString();
		//$absolute_url = JRoute::_($this->payment_params->payment_url);
	$absolute_url = 'mpesa_save.php';
	?>
	<form id="hikashop_mpesa_form" name="hikashop_mpesa_form" action="<?php echo $absolute_url ?>" method="post">
		<div id="hikashop_mpesa_end_image" class="hikashop_mpesa_end_image">
			<div class="col-xs-12 instructions">
			
			</div>
			<table class="table table-striped">
				<tr>
					<td>Mpesa Code </td>
					<td><input type="text" required name="mpesa_code" /></td>
				</tr>
			</table>
			<input id="hikashop_mpesa_button" class="btn btn-primary" type="submit" value="<?php echo JText::_('PAY_NOW');?>" name="" alt="<?php echo JText::_('PAY_NOW');?>" />
		</div>
		<?php
			foreach( $this->vars as $name => $value ) {
				echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars((string)$value).'" />';
			}
			/* $doc = JFactory::getDocument();
			   $doc->addScriptDeclaration("window.addEvent('domready', function() {document.getElementById('hikashop_mpesa_form').submit();});");
			   JRequest::setVar('noform',1);
			*/
		?>
		<script type="text/javascript">
		jQuery(function(){
			jQuery('#hikashop_mpesa_button').on('click',function(){
				var url = 'mpesa_save.php';
				jQuery.post(url,{ data:jQuery('#hikashop_mpesa_form').serialize()},function(datax){
					jQuery('div#hikashop_mpesa_end').html(datax);
					return false;
				});
				return false;
			});
		});
		</script>
	</form>
	<?php } ?>
</div>
