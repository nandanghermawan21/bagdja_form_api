<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="Question")
 */

class Question_model extends CI_Model
{

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
    public $hint;
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

    public function get($where = null, &$refTotal)
    {
        if ($where != null) {
            $this->db->where($where);
        }
        $query = $this->db->get($this->tableName);
        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function add($data = null, &$errorMessage)
    {
        $total = 0;
        $result = null;
        $this->db->insert($this->tableName, $data);
        $result = $this->get(["id" => $this->db->insert_id()], $total);

        if ($total == 1) {
            $errorMessage = "";
            return $result[0];
        } else {
            $errorMessage = $this->db->error(); 
            return null;
        }
    }

    public function update($id, $data, &$errorMessage)
    {
        $total = 0;
        $result = null;

        $this->db->where(["id" => $id]);
        $this->db->update($this->tableName, $data);

        $cc = $this->db->affected_rows();

        if ($cc > 0) {
            $result = $this->get(["id" => $id], $total);
            if ($total == 1) {
                $errorMessage = "";
                return $result[0];
            } else {
                $errorMessage = $this->db->error();
                return null;
            }
        } else {
            return null;
        }
    }

    public function delete($id){
       $total = 0;
        $result = null;
        $result = $this->get(["id" => $id], $total);

        if ($total > 0) {
            $this->db->where("id", $id);
            $this->db->delete($this->tableName);

            $cc = $this->db->affected_rows();
            if ($cc > 0) {
                return $result[0];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}


/**
 * @OA\Schema(schema="QuestionInput")
 */
class QuestionInput
{
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
    public $hint;
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
}
