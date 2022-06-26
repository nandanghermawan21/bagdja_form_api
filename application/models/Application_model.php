<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Application_model extends CI_Model{

    public function getQuestionState($appCode, $stateId, &$refTotal)
    {

        $sql = "SELECT  qt.code as code,
                        qt.name as [name],
                        qt.[type] as [type]
                from sys_application a
                join sys_application_state s on s.application_id = a.id
                join sys_state st on st.id = s.state_id
                join sys_form f on s.form_id = f.id
                join sys_form_page p on p.form_id = f.id
                JOIN sys_page_question g on g.page_id = p.id
                JOIN sys_question_list q on q.group_id = g.group_id
                join sys_question qt on qt.id = q.question_id
                WHERE s.state_id = ? and a.code = '?'";

        $query = $this->db->query($sql,array($$stateId,$appCode));

        $refTotal = $query->num_rows();
        return $query->result();
    }

}

/**
 * @OA\Schema(schema="QuestionState")
 */
class QuestionState
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $code;
    /**
     * @OA\Property()
     * @var integer
     */
    public $name;
    /**
     * @OA\Property()
     * @var integer
     */
    public $type;
}