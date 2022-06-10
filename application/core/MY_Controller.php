<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MY_Controller extends RestController {

  private static $secret = 'sadasdkahdjabdjbdeydgbasdabdan';

    function _createJWToken($user)
    {        
        $payload = [
            'iat' => intval(microtime(true)),
            'exp' => intval(microtime(true)) + (12 * (60 * 60 * 1000)),
            // 'exp' => intval(microtime(true)) + (60),
            'uid' => $user,
        ];

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public function _decode($da)
    {
      
      try{                
        $decoded = JWT::decode($da,new Key(self::$secret, 'HS256'));
    
        return $decoded;
        
      }
      catch (Exception $e) {
        return FALSE;        
        // return $e->getMessage();
      }
    }

    public function _authenticate()
    {
      $headers = $this->input->get_request_header('Authorization', TRUE);
  
      if($headers != null)
      {		
        $token = str_replace('Bearer ',null,$headers);				
        $decoded = $this->_decode($token);
        
        if(!$decoded)
        {
          $data = [
            'success' =>false,
            'message'=>'Unauthorized'
          ];            
          $this->response($data,401);
          die();
        }
        else
        {
          return true;
        }
      }
      else
      {
  
        $data = [
          'success' =>false,
          'message'=>'Unauthorized'
        ];            
        $this->response($data,401);
        die();
      }
      
    }



}
