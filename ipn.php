
<?PHP
//to be placed preferably in the root. 

require_once('configuration.php');
$config 	= new JConfig();
$host 		= $config->host;
$user 		= $config->user;
$prefix 	= $config->dbprefix;
$pass 		= $config->password;
$db 		= $config->db;
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
$email 		= 'pmutwiri@gbc.co.ke';
$to    		= 'pmutwiri@gbc.co.ke,test@gbckenya.com';
$subject 	= 'FALCON EAST AFRICA - IPN Transaction ';
$from 		= 'FALCON EAST AFRICA SAFARICOM IPN <info@falconeastafrica.co.ke>';
$headers  	= 'MIME-Version: 1.0' . "\r\n";
$headers 	.= 'Content-type: text/html; charset=us-ascii' . "\r\n";
$headers 	.= "From: $from \r\n";
$headers 	.= "Reply-To: $email \r\n";
$headers 	.= 'Bcc: clients@gbc.co.ke' . "\r\n";
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
	mail($to, $subject, $body,$headers);
	echo 'OK | Thank you for your payment';
} else {
	$body = 
	'<table border="0">
		<tr><td>IPN has failed</td></tr>
	</table>';
	$subject .= ' - Failure';
	mail($to, $subject, $body,$headers);
	echo ' OK | Mpesa Transaction Failed ';
}
?>
