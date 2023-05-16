<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="OrganitationModel")
 */


class Organitation_model extends CI_Model {

	private $tableName = "csm_organitation";
  	
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
		return $this->db->insert_id();
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

     public function tree($where)
     {
          $this->db->select('*');
          $this->db->from('csm_organitation');
          if($where!=0)
          {
           $this->db->where($where);
          }
          $this->db->join('usm_organitation_level','usm_organitation_level.parent_id=csm_organitation.id','lEFT');
          $this->db->join('usm_users','usm_users.organitation_id=csm_organitation.id','LEFT');
           $query = $this->db->get();
           return $query->result();
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

	/**
     * @OA\Property()
     * @var string
     */

	public $code;

}

/**
 * @OA\Schema(schema="OrganitationTree")
 */

class OrganitationTree {

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

	/**
     * @OA\Property()
     * @var string
     */

	public $code;


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


    /**
     * @OA\Property()
     * @var string
     */

	public $username;


	/**
     * @OA\Property()
     * @var string
     */

	public $nama;


	/**
     * @OA\Property()
     * @var string
     */

	public $alamat;

	/**
     * @OA\Property()
     * @var integer
     */

	public $nohp;


	/**
     * @OA\Property()
     * @var string
     */

	public $email;

     /**
     * @OA\Property()
     * @var string
     */

	public $password;


     /**
     * @OA\Property()
     * @var int
     */

	public $organitation_id;
     
     /**
     * @OA\Property()
     * @var int
     */

	public $parent_user_id;

}