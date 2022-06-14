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

    public function create($data = null, &$errorMessage)
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

    public function delete($id)
    {
        $total = 0;
        $result = null;
        $result = $this->get(["id" => $id], $this->tableName);

        if ($total > 0) {
            $this->db->where(["id" => $id]);
            $this->db->delete($this->tableName);

            $cc = $this->db->affected_rows();
            if ($cc > 1) {
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
 * @OA\Schema(schema="CollectionInput")
 */


class CollectionInput
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
