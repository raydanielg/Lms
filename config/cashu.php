<?php
return [
    'sandbox' => 'https://sandbox.cashu.com/secure/payment.wsdl',
    'live' => 'https://cashu.com/secure/payment.wsdl',
    'follow_sandbox' => 'https://sandbox.cashu.com/cgi-bin/payment/pcashu.cgi',
    'follow_live' => 'https://cashu.com/cgi-bin/payment/pcashu.cgi',
    'trace' => true,  // true or false if default it "true"
    '_testmod' => env('CASHU_TEST_MODE'),  // 0 for test mode or sandbox  1 for live mode
    'secure' => 'default', // default for default encryption  | full to Full Encryption
    '_session_id' => 'cashu',   // any key for  6 char or above
    '_encryption_key' => env('CASHU_ENCRYPTION_KEY'), //encryption  with Service Default or Other Service
    '_merchant_id' => env('CASHU_MERCHANT_ID'),  // marchant name for cashu site | Account Name
];
