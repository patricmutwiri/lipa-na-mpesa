<?php 
define( '_JEXEC', 1 ); 
define( '_VALID_MOS', 1 ); 
define( 'JPATH_BASE', realpath(dirname(__FILE__))); 
define( 'DS', DIRECTORY_SEPARATOR ); 
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' ); 
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' ); 
$mainframe = JFactory::getApplication('site'); 
$mainframe->initialise(); 
$app 	=	JFactory::getApplication();
$input = JFactory::getApplication()->input;
$data_base = JFactory::getDBO();
//add joomla framework support
if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')){
	return 'This module can not work without the Hikashop Component';
}
//hikashop helper loading
$hikashop = new hikashop();
//get this order's id
$qu = 'select * from #__hikashop_order where mpesa_code = '.$input->get('mpesa_code').'';
$db->setQuery($qu);
$result = $db->loadObject();
$orderid = $result->order_id;
$fullprice = $result->order_full_price;
$order_details = $hikashop->getOrder($orderid);
?>
<?php
require_once('configuration.php');
$config 	= new JConfig();
$host 		= $config->host;
$user 		= $config->user;
$prefix 	= $config->dbprefix;
$pass 		= $config->password;
$db 		= $config->db;
$mailfrom   = $config->mailfrom;
$mailfromname = $config->fromname;
$sent 		= 0;
$id 		= $_REQUEST['id']; //id
$origin 	= $_GET['orig'];//origin of the transaction
$dest 		= $_GET['dest'];//destination
$tstamp 	= $_GET['tstamp'];//time stamp
$text 		= $_GET['text'];//text
$mpesa_code = $_GET['mpesa_code'];//mpesa code
$mpesa_acc 	= $_GET['mpesa_acc'];//mpesa acc
$mpesa_msisdn 	= $_GET['mpesa_msisdn'];//mpesa msisdn
$mpesa_trx_date = $_GET['mpesa_trx_date'];//mpesa transaction date
$mpesa_trx_time = $_GET['mpesa_trx_time'];//mpesa transaction time
$mpesa_amt 		= $_GET['mpesa_amt'];//mpesa amount
$mpesa_sender 	= $_GET['mpesa_sender'];//mpesa sender
$ip 		= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';//ip
$connHost 	= mysql_connect($host,$user,$pass) or die(mysql_error());//online
$connDb 	= mysql_select_db($db) or die(mysql_error());//online
$email 		= 'patwiri@gmail.com';
$to    		= $email;
$sitename   = strtoupper($config->sitename);
$subject 	= $sitename.' - IPN Transaction ';
$from 		= $sitename.' SAFARICOM IPN <'.$mailfrom.'>';
$headers  	= 'MIME-Version: 1.0' . "\r\n";
$headers 	.= 'Content-type: text/html; charset=us-ascii' . "\r\n";
$headers 	.= "From: $from \r\n";
$headers 	.= "Reply-To: $email \r\n";
$headers 	.= 'Bcc: '.$email.'' . "\r\n";
if(!(!$connHost) && !(!$connDb)){
	//dump data in database
	$dumpTableData = mysql_query("INSERT INTO ".$prefix."mpesa_ipn (idTrx,origin,destination,timeStamp,text,mpesaCode,mpesaAccount,mpesaMSISDN,mpesaTrxDate,mpesaTrxTime,mpesaAmt,mpesaSender,ip,sent) VALUES('$id','$origin','$dest','$tstamp','$text','$mpesa_code','$mpesa_acc','$mpesa_msisdn','$mpesa_trx_date','$mpesa_trx_time','$mpesa_amt','$mpesa_sender','$ip','$sent')") or die(mysql_error());
	$subject .= ' - Success';
	$body = 
	'<table border="0">
		<tr><td>ID:' .$id. '</td><td>Origin:' .$origin. '</td></tr>
		<tr><td>Destination:' .$dest. '</td><td>Time Stamp:' .$tstamp. '</td></tr>
		<tr><td>Text:' .$text. '</td><td>Mpesa Code:' .$mpesa_code. '</td></tr>
        <tr><td>Mpesa Account:' .$mpesa_acc. '</td><td>Mpesa MSISDN:' .$mpesa_msisdn. '</td></tr>
		<tr><td>Mpesa Transaction Date:' .$mpesa_trx_date. '</td><td>Mpesa Transaction Time:' .$mpesa_trx_time. '</td></tr>
		<tr><td>Mpesa Amount:' .$mpesa_amt. '</td><td>Mpesa Sender:' .$mpesa_sender. '</td></tr>
	</table>';									
	// mark mpesa table status paid/done
	$mpesatbl  = new stdClass();
	$mpesatbl->t_id = $input->get('mpesa_code');
	if($mpesa_amt >= $fullprice){
		// mark order paid
		$hikashop->modifyOrder($orderid,'confirmed',true,true,null); //order confirmed if price ok
		$mpesatbl->status = 'paid';
		$body .= '<p> Order Amount Paid the actual price of KES. '.$fullprice.'</p>';
	} else{
		$mpesatbl->status = 'created';
		$body .= '<p>Order Amount Paid less than the actual price of KES. '.$fullprice.'.<br/> Please Repay with the full Amount. </p>';
	}
	$data_base->updateObject('#__mpesa',$mpesatbl,'t_id');
	//send mail later
	mail($to, $subject, $body,$headers);
	echo ' OK | Thank you for your payment';
} else {
	$body = 
	'<table border="0">
		<tr><td>IPN has failed</td></tr>
	</table>';
	$subject .= ' - Failure';
	mail($to, $subject, $body,$headers);
	echo ' OK | Mpesa Transaction Failed ';
} ?>
