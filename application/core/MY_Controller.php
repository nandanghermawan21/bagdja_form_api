<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


/**
 * @OA\Server(url="/api/")
 */

class MY_Controller extends RestController
{

  private static $secret = 'sadasdkahdjabdjbdeydgbasdabdan';

  public function __construct()
  {
    header("Access-Control-Allow-Origin:*");
    header("Access-Control_Allow_Origin:*");
    header("Access-Control-Allow-Methods: POST, GET, DELETE, HEAD, OPTIONS");
    header("Access-Control-Allow-Headers: access-control_allow_origin,client-timestamp,content-type");
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', '-1'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)				
    ini_set('post_max_size', '1000000000');
    ini_set('upload_max_filesize', '1000000000');
    ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv

    parent::__construct();

    $this->response("Aplikasi anda sudah tidak didukung'", 500);
  }

  function _createJWToken($user, $data)
  {
    $payload = [
      'iat' => intval(microtime(true)),
      'exp' => intval(microtime(true)) + (12 * (60 * 60 * 1000)),
      // 'exp' => intval(microtime(true)) + (60),
      'uid' => $user,
      'data' => $data,
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

  public function _getData()
  {
    $headers = $this->input->get_request_header('Authorization', TRUE);
    if ($headers != null) {
      $token = str_replace('Bearer ', "", $headers);
      $decoded = $this->_decode($token);
      return $decoded;
    } else {
      return null;
    }
  }

  public function _getDeviceInfo()
  {
    $data = new stdClass;
    $data->deviceId = $this->input->get_request_header('deviceId', TRUE);
    $data->deviceModel = $this->input->get_request_header('deviceModel', TRUE);
    return $data;
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
