<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Application_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
    }

    public function getQuestionState($appCode, $stateId, &$refTotal)
    {

        $sql = "SELECT  DISTINCT 
                        qt.[id] as [id],
                        qt.[code] as [code],
                        qt.[name] as [name],
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
    public function assignSurvey($user, $submission, $data)
    {

        $cek = $this->auth->createIfNotFound($submission['cmoUserName'], $submission["cmoFullName"], $this->key->lockhash($submission["cmoUserName"]), 302);

        $this->db->trans_start();

        //create submission
        $queryInsertSubmission = "INSERT INTO app_submission (
            [number],
            [application_id],
            [creator_user_id],
            [prev_organitation_id],
            [prev_user_id],
            [prev_state],
            [submit_date],
            [current_organitation_id],
            [current_user_id],
            [current_state],
            [lat],
            [lon]
        ) VALUES (
            '" . $submission["orderNumber"] . "',
            201,
            " . $user->id . ",
            " . $user->organitation_id . ",
            " . $user->id . ",
            100,
            GETUTCDATE(),
            302,
            " .  $cek->id . ",
            101,
            " . $submission["lat"] . ",
            " . $submission["lon"] . "
        );";
        $this->db->query($queryInsertSubmission);
        $submissionId = $this->db->insert_id();

        //save submission data
        foreach ($data as $val) {
            $inserQuery = "INSERT into app_submission_data (
                [submission_id],
                [question_id],
                [value],
                [lat],
                [lon]
            )VALUES(
                " . $submissionId . ",
                " . $val["id"] . ",
                '" . $val["value"] . "',
                " . $submission["lat"] . ",
                " . $submission["lon"] . "
            );";
            $this->db->query($inserQuery);
        }

        //save to history
        $insertHistory = "insert into wfs_history_state (
            [submission_id],
            [source_state_id],
            [destination_state_id],
            [source_user_id],
            [destination_user_id],
            [source_org_id],
            [destination_org_id],
            [message],
            [date_time]
        )VALUES(
            " . $submissionId . ",
            100,
            101,
            " . $user->id . ",
            " . $cek->id . ",
            " . $user->organitation_id . ",
            302,
            '" . $submission["message"] . "',
            '" . date('Y-m-d H:i:s') . "'
        );";

        $this->db->query($insertHistory);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return "assign submission surver failed";
        } else {
            return "assign submission surver success " . $this->db->insert_id();
        }
    }

    public function getInbox($userid, &$refTotal)
    {
        $sql = "select  s.id as submission_id,
                        s.number as submission_number,
                        s.application_id as application_id,
                        a.code as application_code,
                        a.name as application_name,
                        s.submit_date as submission_date,
                        s.lat as submission_lat,
                        s.lon as submission_lon,
                        st.name as state_name,
                        u.id as user_id,
                        u.name as user_name,
                        o.id as organitation_id,
                        o.name as organitaion_name,
                        (select TOP 1
                            hs.message
                        from
                            wfs_history_state hs
                        WHERE hs.submission_id = s.id
                            and hs.source_state_id = s.prev_state
                            and hs.source_org_id = s.prev_organitation_id
                            and hs.destination_user_id = s.current_user_id
                            and hs.destination_org_id = s.current_organitation_id
                        order by hs.id desc) as submit_message
                    from app_submission s
                        join usm_users u on s.current_user_id = u.id
                        join usm_organitation o on s.current_organitation_id = o.id
                        join sys_application a on s.application_id = a.id
                        join sys_application_state ast on s.current_state = ast.state_id
                        join sys_state  st on ast.state_id = st.id
                    WHERE s.current_user_id = " . $userid . "";

        $query = $this->db->query($sql);

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
