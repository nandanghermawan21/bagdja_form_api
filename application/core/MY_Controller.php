<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




class MY_Controller extends RestController
{

  private static $secret = 'sadasdkahdjabdjbdeydgbasdabdan';

  public function __construct()
  {
    header("Access-Control-Allow-Origin:*");
    header("Access-Control_Allow_Origin:*");
		header("Access-Control-Allow-Methods: POST, GET, DELETE, HEAD, OPTIONS");
		header("Access-Control-Allow-Headers: access-control_allow_origin,client-timestamp,content-type");
    parent::__construct();
  }

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

    try {
      $decoded = JWT::decode($da, new Key(self::$secret, 'HS256'));

      return $decoded;
    } catch (Exception $e) {
      return FALSE;
      // return $e->getMessage();
    }
  }

  public function _authenticate()
  {
    $headers = $this->input->get_request_header('Authorization', TRUE);

    if ($headers != null) {
      $token = str_replace('Bearer ', "", $headers);
      $decoded = $this->_decode($token);

      if (!$decoded) {
        $this->response('Unauthorized', 401);
        die();
      } else {
        return true;
      }
    } else {
      $this->response("Unauthorized'", 401);
      die();
    }
  }
}
