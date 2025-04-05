<?php
return [
	'sandbox'  =>'https://sandbox.cashu.com/secure/payment.wsdl',  
	'live'  =>'https://cashu.com/secure/payment.wsdl',  
	'follow_sandbox'  =>'https://sandbox.cashu.com/cgi-bin/payment/pcashu.cgi',  
	'follow_live'  =>'https://cashu.com/cgi-bin/payment/pcashu.cgi',  
	'trace'  =>true,  // true or false if default it "true"
	'_testmod'  =>'0',  // 0 for test mode or sandbox  1 for live mode
	'secure'  =>'default', // default for default encryption  | full to Full Encryption
	'_session_id'  =>'',   // any key for  6 char or above 
	'_encryption_key'=>'', //encryption  with Service Default or Other Service 
	'_merchant_id'  =>'',  // marchant name for cashu site | Account Name
];