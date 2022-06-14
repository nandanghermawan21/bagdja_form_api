<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="CollectionModel")
 */


class Collection_model extends CI_Model
{
    private $tableName = "sys_collection";
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

    public function update($where, $data, $table)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
        return $this->db->affected_rows();
    }

    public function delete($data, $table)
    {
        $this->db->where($data);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }
}


/**
 * @OA\Schema(schema="collectionInputList")
 */


class collectionInputList
{
    /**
     * @OA\Property()
     * @var string
     */
    public $name;
}

/**
 * @OA\Schema(schema="collectionInputData")
 */


class collectionInputData
{

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
