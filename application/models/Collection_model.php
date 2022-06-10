<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="CollectionModelList")
 */


class Collection_model extends CI_Model {
	
  	
	public function view($where = null, $table)
	{
        if($where != null)
        {
            $this->db->where($where);
        }
		$query = $this->db->get($table);
		return $query->result();
	}

     public function insert($data,$table)
     {
          $this->db->insert($table,$data);
            return $this->db->insert_id();        
     }


     public function update($where,$data,$table)
	{
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows(); 	
	}

     public function delete($data,$table)
     {
           $this->db->where($data);
           $this->db->delete($table);   
           return $this->db->affected_rows(); 	
     }

	/**
     * @OA\Property()
     * @var integer
     */

	public $id;
     
     /**
     * @OA\Property()
     * @var string
     */

    public $name;

}


/**
 * @OA\Schema(schema="collectionInputList")
 */


class collectionInputList {

    /**
     * @OA\Property()
     * @var string
     */

    public $name;


}

/**
 * @OA\Schema(schema="collectionInputData")
 */


class collectionInputData {

    /**
     * @OA\Property()
     * @var integer
     */

    public $collection_id;



    /**
     * @OA\Property()
     * @var string
     */

    public $label;


    /**
     * @OA\Property()
     * @var string
     */

    public $value;

}


