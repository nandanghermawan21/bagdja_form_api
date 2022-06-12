<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="Responses")
 */
class Responses_model extends CI_Model{
     /**
     * @OA\Property()
     * @var String
     */
    public $status;

    /**
     * @OA\Property()
     * @var String
     */
    public $message;

    /**
     * @OA\Property()
     * @var object
     */
    public $data;

    /**
     * @OA\Property()
     * @var object
     */
    public $summary;

    /**
     * @OA\Property()
     * @var int
     */
    public $total;

    public function successWithData($data, $total = null){
        $response = new Responses_model();
        $response->status = "200";
        $response->message = "Success";
        $response->data = $data;
        $response->total = $total;
        
        return $response;
    }

    public function error($message){
        $response = new Responses_model();
        $response->status = "ERR-001";
        $response->message = $message;
        
        return $response;
    }

    public function badRequwst($message){
        $response = new Responses_model();
        $response->status = "ERR-400";
        $response->message = $message;
        
        return $response;
    }
}