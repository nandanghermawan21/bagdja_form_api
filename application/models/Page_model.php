<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="PageModel")
 */


class Page_model extends CI_Model
{
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

    public function getQuestions($pageId, &$refTotal)
    {
        $sql = "select q.page_id,
                        q.group_id,
                        q.[order],
                        g.[code],
                        g.[name]
                from sys_page_question as q
                JOIN sys_question_group g on q.[group_id] = g.id
                where q.page_id = ?
                order BY q.[order] asc";

        $query = $this->db->query($sql, array($pageId));
        $refTotal = $query->num_rows();
        
        return $query->result();
    }
}

/**
 * @OA\Schema(schema="PageQuestionDetail")
 */
class QuestionGroupData
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
