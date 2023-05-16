<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="LevelModel")
 */


class Level_model extends CI_Model {

	private $tableName = "usm_organitation_level";
  	
	public function view($where = null)
	{
        if($where != null)
        {
            $this->db->where($where);
        }
		$query = $this->db->get($this->tableName);
		return $query->result();
	}

     public function insert($data)
     {
          $this->db->insert($this->tableName,$data);
		// return $this->db->insert_id();
        return $this->db->affected_rows(); 	
     }


     public function update($where,$data)
	{
		$this->db->where($where);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows(); 	
	}

     public function delete($data)
     {
           $this->db->where($data);
           $this->db->delete($this->tableName);   
           return $this->db->affected_rows(); 	
     }

	/**
     * @OA\Property()
     * @var integer
     */

	public $parent_id;
     
     /**
     * @OA\Property()
     * @var integer
     */

    public $child_id;



}
