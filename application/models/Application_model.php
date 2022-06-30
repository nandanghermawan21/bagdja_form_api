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

        // $cek = $this->auth->login(["username" => $submission['cmoUserName'], "organitation_id" => 302]);

        // if ($cek == false) {
        //     $queryInsertUser = "INSERT INTO usm_users (
        //         [username],
        //         [password],
        //         [name],
        //         [organitation_id]) VALUES (
        //             '" . $submission["cmoUserName"] . "',
        //             '" . $this->key->lockhash($submission["cmoUserName"]) . "',
        //             '" . $submission["cmoFullName"] . "',
        //             302
        //         );";
        //     $this->db->query($queryInsertUser);
        //     $cek = $this->auth->login(["username" => $submission['cmoUserName'], "organitation_id" => 302]);
        // }

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
            " . date('Y-m-d H:i:s') . "
        );";

        $this->db->query($insertHistory);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return "assign submission surver failed";
        } else {
            return "assign submission surver success " . $this->db->insert_id();
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
