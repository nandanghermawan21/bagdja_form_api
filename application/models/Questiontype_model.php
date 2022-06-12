<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="Question")
 */

 class Questiontype_model extends CI_Model{
     private $tablename = "sys_question_type";

    /**
     * @OA\Property()
     * @var String
     */
    public $code;

    /**
     * @OA\Property()
     * @var String
     */
    public $name;


    public function get($where = null)
	{
        if($where != null)
        {
            $this->db->where($where);
        }
		$query = $this->db->get($this->tablename);
        
		return $query->result();
	}
 }