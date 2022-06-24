<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="DicissionType")
 */

 class Dicissiontype_model extends CI_Model{
    private $tablename = "sys_dicission_type";

     /**
     * @OA\Property()
     * @var String
     */
    public $id;

    /**
     * @OA\Property()
     * @var String
     */
    public $name;

    /**
     * @OA\Property()
     * @var String
     */
    public $symbol;


    public function get($where = null, &$refTotal = 0)
	{
        if($where != null)
        {
            $this->db->where($where);
        }
		$query = $this->db->get($this->tablename);

        $refTotal = $query->num_rows();

		return $query->result();
	}
 }

