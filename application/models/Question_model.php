<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="Question")
 */

 class Question_model extends CI_Model{

     private $tableName = "sys_question";
    // [id]            INT            NOT NULL,
    // [code]          VARCHAR (50)   NULL,
    // [name]          VARCHAR (1000) NULL,
    // [label]         VARCHAR (1000) NULL,
    // [type]          VARCHAR (20)   NULL,
    // [collection_id] INT            NULL,

     /**
     * @OA\Property()
     * @var int
     */
    public $id;
     /**
     * @OA\Property()
     * @var string
     */
    public $code;
     /**
     * @OA\Property()
     * @var string
     */
    public $name;
     /**
     * @OA\Property()
     * @var string
     */
    public $label;
     /**
     * @OA\Property()
     * @var string
     */
    public $type;
     /**
     * @OA\Property()
     * @var int
     */
    public $collection_id;

    public function get($where = null)
	{
        if($where != null)
        {
            $this->db->where($where);
        }
		$query = $this->db->get($this->tableName);
		return $query->result();
	}
 }