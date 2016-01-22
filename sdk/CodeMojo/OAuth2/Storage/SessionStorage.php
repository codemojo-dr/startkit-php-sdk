<?php

namespace CodeMojo\OAuth2\Storage;

class SessionStorage {
    
    /**
     * TokenStorage constructor.
     */
    public function __construct()
    {
        if('' == session_id()){
            session_start();
        }
    }

    public function storeAccessToken($token,$expiry){
        $_SESSION['oauth2_access_token'] = base64_encode($token);
        $_SESSION['oauth2_expiry'] = time() + $expiry;
    }

    public function getAccessToken(){
        return base64_decode($_SESSION['oauth2_access_token']);
    }

    public function accessTokenMightHaveExpired(){
        return (!isset($_SESSION['oauth2_expiry']) || (time() > $_SESSION['oauth2_expiry']));
    }


}