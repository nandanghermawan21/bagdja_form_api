<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="AuthModel")
 */

class Auth_model extends CI_Model {

	function login($where)
	{
		$query = $this->db->get_where('usm_users',$where,1);
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else {
			return false;
		}
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
     * @var string
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


     /**
     * @OA\Property()
     * @var string
     */

	public $token;




}
