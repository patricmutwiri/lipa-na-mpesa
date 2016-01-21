<?php 
// Get Joomla! framework 
define( '_JEXEC', 1 ); 
define( '_VALID_MOS', 1 ); 
define( 'JPATH_BASE', realpath(dirname(__FILE__))); 
define( 'DS', DIRECTORY_SEPARATOR ); 
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' ); 
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' ); 
$mainframe =& JFactory::getApplication('site'); 
$mainframe->initialise(); 
?>
<?php 
//save mpesa code
		$vars = JFactory::getApplication()->input;
		$user  = JFactory::getUser();
		$db    = JFactory::getDBO();
		$mpesa = new stdClass();
		$mpesa->id = NULL;
		$mpesa->t_id = $vars->get('mpesa_code');
		$mpesa->order_id = $vars->get('ORDERID');
		$mpesa->t_date   = date('d-m-Y',time());
		$mpesa->name = $user->name;
		$mpesa->email = $user->email;
		if($db->insertObject('#__mpesa',$mpesa,'id')) {
			echo  ' Payment Successfull ';
		} else {
			echo  ' Error ';
		}
?>
