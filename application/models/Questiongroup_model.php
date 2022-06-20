<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="QuestionGroupModel")
 */

class Questiongroup_model extends CI_Model
{
    private $tableName = "sys_question_group";
    private $dataTableName = "sys_question_list";
    private $questionTableName = "sys_question";

    /**
     * @OA\Property()
     * @var integer
     */
    public $id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $code;
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

    public function getData($where = null, &$refTotal)
    {
        $whereArray = array();
        foreach (array_keys($where) as $key) {
            array_push($whereArray, "[" . $key . "]" . " = " . $where[$key]);
        }

        $sql = "select a.*, b.code, b.name, b.label, b.hint, b.[type], b.collection_id from sys_question_list as a
        join sys_question b on a.question_id = b.id ";

        if (count($whereArray) > 0) {
            $sql = $sql . " where " . implode(" and ", $whereArray);
        }

        $sql = $sql . " order by [order] asc ";

        $this->db->query($sql);
        $query = $this->db->query($sql);

        // $query = $this->db->get($this->dataTableName);
        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function addData($data = null, &$errorMessage)
    {
        $total = 0;
        $result = null;
        $this->db->insert($this->dataTableName, $data);
        $result = $this->getData($data, $total);

        $this->reorder($data["group_id"],$data["question_id"],$data["order"]);

        if ($total == 1) {
            $errorMessage = "";
            return $result[0];
        } else {
            $errorMessage = $this->db->error();
            return null;
        }
    }

    public function updateData($id,  $questionId,  $data, &$errorMessage)
    {
        $total = 0;
        $result = null;

        $this->db->where(["group_id" => $id, "question_id" => $questionId]);
        $this->db->update($this->dataTableName, $data);

        $cc = $this->db->affected_rows();

        $this->reorder($data["group_id"],$data["question_id"],$data["order"]); 

        if ($cc > 0) {
            $result = $this->getData($data, $total);
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

    public function deleteData($id, $questionId)
    {
        $total = 0;
        $result = null;
        $result = $this->getData(["group_id" => $id, "question_id" => $questionId], $total);

        if ($total > 0) {
            $this->db->where(["group_id" => $id, "question_id" => $questionId]);
            $this->db->delete($this->dataTableName);

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

    public function reorder($groupId, $questionId, $target)
    {
        $sql = "update sys_question_list 
        set [order] = [order] + 1
        where [group_id] = ? and [question_id] != ? and [order] >= ?";

        $query = $this->db->query($sql, array($groupId, $questionId, $target) );

        return $query;
    }
}

/**
 * @OA\Schema(schema="QuestionGroupInput")
 */
class QuestionGroupInput
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
}

/**
 * @OA\Schema(schema="QuestionGroupData")
 */
class QuestionGroupData
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $group_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $question_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $order;
}
