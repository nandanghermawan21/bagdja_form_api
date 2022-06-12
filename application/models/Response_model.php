<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="Response")
 */
class Response_model extends CI_Model{
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
        $response = new Response_model();
        $response->status = 100;
        $response->message = "Success";
        $response->data = $data;
        $response->total = $total;
    }
}