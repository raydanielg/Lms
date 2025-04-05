<?php
namespace CashUAony\Phpanonymous;

use App\Http\Controllers\Controller; 
use SoapClient;
use Config;
class CashU extends Controller
{
 
    public static function Go($data)
    {
        ini_set("soap.wsdl_cache_enabled", "1");
        ini_set('user_agent','Mozilla/5.0 (Windows NT6.1 ;WOW64;rv:20.0) Gecko/20100101 Firefox/20.0');

        if(config('casuh.secure') == 'full')
        {
         $token = md5(config('cashu._merchant_id').':'.$data['amount'].':'.strtolower($data['currency']).':'.strtolower(config('cashu._session_id')).':'.config('cashu._encryption_key'));
        }else{   
         $token = md5(strtolower(config('cashu._merchant_id')).':'.$data['amount'].':'.strtolower($data['currency']).':'.config('cashu._encryption_key'));
        }


        if(config('cashu._testmod') == 0)
        {
            $urlcash     = config('cashu.sandbox');
            $follow_link = config('cashu.follow_sandbox');
        }else{
            $urlcash = config('cashu.live');
            $follow_link = config('cashu.follow_live');
        }
            $client  =  new SoapClient($urlcash,['trace' => true]); 
            $request =  $client->DoPaymentRequest(
            config('cashu._merchant_id'),    // marchant_id
            $token,      // token with encryption keyword md5 in cashu setup services
            $data['display_text'],          // display text with item product
            $data['currency'],              // set currency want pay
            $data['amount'],                // amount with your form request html
            $data['lang'],                  // language want user used
            config('cashu._session_id'),     // create custom session with private variable
            $data['item1'],                  // text item 1 explained 
            $data['item2'],                  // text item 2 explained
            $data['item3'],                  // text item 3 explained
            $data['item4'],                  // text item 4 explained
            $data['item5'],                  // text item 5 explained
            config('cashu._testmod'),        // here is test mode "0" to use sandbox,"1" to use live mode
            $data['service_name']            // services payment name
            ); 

       // return dd($request);
            $tmp              = strstr($request, '=');
            $Transaction_Code = substr($tmp, 1);
            $data = '<form name="redirect" action="'.$follow_link.'" method="POST">
                     <input type="hidden" name="Transaction_Code" value="'.$Transaction_Code.'">
                     </form>
                     <script type="text/javascript">
                     document.redirect.submit();
                     </script>
            ';
            return $data;

            return redirect($result);
          /*  $ch = curl_init($follow_link);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $html = curl_exec($ch);
            $redirectURL = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
            curl_close($ch);
            return $html;/*/
            /*
      //      return dd($Transaction_Code);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $follow_link);
             curl_setopt($ch, CURLOPT_POST      ,1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
             curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
             curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
             return dd($ch);
            return dd(curl_exec($ch));*/
    }




 
}

 