<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="PageModel")
 */


class Page_model extends CI_Model
{
    private $pageTableName = "sys_form_page";
    private $pageQuestionTableName = "sys_page_question";

    /**
     * @OA\Property()
     * @var int
     */
    public $id;
    /**
     * @OA\Property()
     * @var int
     */
    public $form_id;
    /**
     * @OA\Property()
     * @var string
     */
    public $name;
    /**
     * @OA\Property()
     * @var int
     */
    public $order;

    public function get($where = null, &$refTotal)
    {
        if ($where != null) {
            $this->db->where($where);
        }
        $query = $this->db->get($this->pageTableName);
        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function create($data = null, &$errorMessage)
    {
        $total = 0;
        $result = null;
        $this->db->insert($this->pageTableName, $data);
        $result = $this->get(["id" => $this->db->insert_id()], $total);

        $this->reorderPages($result["id"],$result["form_id"],$data["order"]);

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
        $this->db->update($this->pageTableName, $data);

        $cc = $this->db->affected_rows();
        $this->reorderPages($id,$data["form_id"],$data["order"]);

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

    public function reorderPages($pageId, $formId, $target)
    {
        $sql = "update sys_form_page 
        set [order] = [order] + 1
        where [id] = ? and [form_id] != ? and [order] >= ?";

        $query = $this->db->query($sql, array($pageId, $formId, $target) );

        return $query;
    }

    public function getQuestions($where = null, &$refTotal)
    {
        $whereArray = array();
        foreach (array_keys($where) as $key) {
            array_push($whereArray, "[" . $key . "]" . " = " . $where[$key]);
        }

        $sql = "select q.page_id,
                        q.group_id,
                        q.[order],
                        g.[code],
                        g.[name]
                from sys_page_question as q
                JOIN sys_question_group g on q.[group_id] = g.id ";

        if (count($whereArray) > 0) {
            $sql = $sql . " where " . implode(" and ", $whereArray);
        }

        $sql = $sql . " order BY q.[order] asc ";

        $this->db->query($sql);
        $query = $this->db->query($sql);

        // $query = $this->db->get($this->dataTableName);
        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function addQuestion($data = null, &$errorMessage)
    {
        $total = 0;
        $result = null;
        $this->db->insert($this->pageQuestionTableName, $data);
        $result = $this->getQuestions($data, $total);

        $this->reorderQuestions($data["page_id"],$data["group_id"],$data["order"]);

        if ($total == 1) {
            $errorMessage = "";
            return $result[0];
        } else {
            $errorMessage = $this->db->error();
            return null;
        }
    }

    public function updateQuestion($id,  $groupId,  $data, &$errorMessage)
    {
        $total = 0;
        $result = null;

        $this->db->where(["page_id" => $id, "group_id" => $groupId]);
        $this->db->update($this->pageQuestionTableName, $data);

        $cc = $this->db->affected_rows();

        $this->reorderQuestions($data["page_id"],$data["group_id"],$data["order"]); 

        if ($cc > 0) {
            $result = $this->getQuestions($data, $total);
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

    public function deleteQuestion($id, $groupId)
    {
        $total = 0;
        $result = null;
        $result = $this->getQuestions(["page_id" => $id, "group_id" => $groupId], $total);

        if ($total > 0) {
            $this->db->where(["page_id" => $id, "group_id" => $groupId]);
            $this->db->delete($this->pageQuestionTableName);

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

    public function reorderQuestions($pageId, $groupId, $target)
    {
        $sql = "update sys_page_question 
        set [order] = [order] + 1
        where [page_id] = ? and [group_id] != ? and [order] >= ?";

        $query = $this->db->query($sql, array($pageId, $groupId, $target) );

        return $query;
    }


}

/**
 * @OA\Schema(schema="PageQuestionDetail")
 */
class PageQuestionDetail
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $page_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $group_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $order;
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
 * @OA\Schema(schema="PageQuestion")
 */
class PageQuestion
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $page_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $group_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $order;
}


