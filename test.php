<?php
$tokens = "APA91bFy_EZQBSBpfUJoFxewoETaTxF43kqHiwDTC3uKfwigcNZjGgMY3I4knHqtYm0cBcstmGTYt-phtyZuyxeaaShQghZU1418Q5BGuCXHjjdDCp_Cz-iq2gBqqbyPhHrgyIeBfc6yeIV2YiLOFbRgwI7t2buXEkloAFOwV5n8CK2weEu9xfg";

       if (!is_array($tokens)) {
            $tokens = array($tokens);
        }

        $headers = array(
            'Content-Type: application/json;charset=UTF-8',
            'Authorization:key=643419720869'
        );

        $json = array(
            "registration_ids" => $tokens,
            "data" => array(
                "message" => $message,
            ),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));

        $response = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

        echo $response;

        if (curl_errno($ch)) {  
            return false;
        }  
        if ($httpCode != 200) {  
            return false;  
        }  
        curl_close($ch);  

        return $response;



?>