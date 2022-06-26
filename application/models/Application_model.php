<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Application_model extends CI_Model
{

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
                WHERE s.state_id = " . $stateId . " and a.code = '" . $appCode . "'";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();
        return $query->result();
    }

    //cuntion khusus merecord order ke submission and set current user to CMO
    public function assignSurvey($submission, $data, $createNewUser = true)
    {
        $this->db->trans_start();

        //create user 
        if ($createNewUser == true) {
            $queryInsertUser = "INSERT INTO usm_users (
                                            [username],
                                            [password],
                                            [name],
                                            [organitation_id]) VALUES (
                                                '".$submission["cmoUserName"]."',
                                                '".$this->key->lockhash($submission["cmoUserName"])."',
                                                '".$submission["cmoFullName"]."',
                                                3
                                            );";
        }
        $this->db->query($queryInsertUser);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
           return "assign submission surver failed";
        }else{
           return "assign submission surver success";
        }
    }
}

/**
 * @OA\Schema(schema="QuestionState")
 */
class QuestionState
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
    public $type;
}

/**
 * @OA\Schema(schema="QuestionValueInput")
 */
class QuestionValueInput
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
    public $value;
}
