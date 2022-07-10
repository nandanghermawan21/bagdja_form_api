<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Application_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->model('Form_model', 'form');
        $this->load->model('Page_model', 'page');
        $this->load->model('Questiongroup_model', 'group');
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


    public function getSnapShoot($submission_id, &$refTotal)
    {
        $sql = "SELECT  q.id,
                        q.code,
                        q.label,
                        sd.[value]
                from app_submission s
                join sys_application_state ast on s.current_state = ast.state_id
                join sys_question_list ql on ast.question_group_id = ql.group_id
                join sys_question q on ql.question_id = q.id
                LEFT JOIN app_submission_data sd on q.id = sd.question_id and s.id = sd.submission_id
                WHERE s.id = " . $submission_id . "";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function getFormRef($submission_id, &$refTotal)
    {
        $sql = "SELECT  f.id as id,
                        f.code as code,
                        f.name as [name]
                from app_submission s
                join sys_application_state_ref asr on s.application_id = asr.application_id AND s.current_state = asr.state_id
                join sys_form f on asr.form_id = f.id
                WHERE s.id = " . $submission_id . "";

        $query = $this->db->query($sql);
        $refTotal = $query->num_rows();
        $result = $query->result();

        return  $this->getFormComponent($submission_id, $result);
    }

    public function getForm($submission_id, &$refTotal)
    {
        $sql = "SELECT  f.id as id,
                        f.code as code,
                        f.name as [name]
                from app_submission s
                join sys_application_state asr on s.application_id = asr.application_id AND s.current_state = asr.state_id
                join sys_form f on asr.form_id = f.id
                WHERE s.id = " . $submission_id . "";

        $query = $this->db->query($sql);
        $refTotal = $query->num_rows();
        $result = $query->result();

        return  $this->getFormComponent($submission_id, $result)[0];
    }

    public function getFormComponent($submission_id, $forms)
    {
        foreach ($forms as $form) {
            $form->totalPage = 0;
            $form->pages = $this->form->getFormPages($form->id, $form->totalPage);
            $form->totalData = 0;
            $form->data = $this->getSubmissionData($submission_id, $form->totalData76);

            foreach ($form->pages as $page) {
                $page->totalQuestionGroup = 0;
                $page->questionGroups = $this->page->getQuestions(["page_id" => $page->id], $page->totalQuestionGroup);

                $page->totalDicission = 0;
                $page->dicissions = $this->page->getDicission(["page_id" => $page->id], $page->totalQuestionGroup);

                foreach ($page->questionGroups as $group) {
                    $group->totalQuestions = 0;
                    $group->questions = $this->group->getData(["group_id" => $group->group_id], $group->totalQuestions);

                    foreach ($group->questions as $question) {
                        $question->form_id = $form->id;
                        $question->page_id = $page->id;
                    }
                }
            }
        }

        return $forms;
    }

    //cuntion khusus merecord order ke submission and set current user to CMO
    public function assignSurvey($user, $submission, $data, $deviceInfo)
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
            [lon],
            [current_state_status],
            [current_state_message],
            [current_device_id],
            [current_model_id],
            [updated_by],
            [updated_date]
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
            " . $submission["lon"] . ",
            'SUBMITED',
            '" . $submission["message"] . "',
            '" . $deviceInfo->deviceId . "',
            '" . $deviceInfo->deviceModel . "',
            " . $user->id . ",
            'GETUTCDATE()'
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
            [date_time],
            [status],
            [created_by],
            [device_id],
            [device_name]
        )VALUES(
            " . $submissionId . ",
            100,
            101,
            " . $user->id . ",
            " . $cek->id . ",
            " . $user->organitation_id . ",
            302,
            '" . $submission["message"] . "',
            'GETUTCDATE()',
            'SUBMITED',
            " . $user->id . ",
            '" . $deviceInfo->deviceId . "',
            '" . $deviceInfo->deviceModel . "'
        );";

        $this->db->query($insertHistory);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return "assign submission surver failed";
        } else {
            return "assign submission surver success " . $this->db->insert_id();
        }
    }

    public function getSubmissionData($submission_id, &$refTotal)
    {
        $sql = "select  submission_id,
                        question_id,
                        [value],
                        lat,
                        lon
                from app_submission_data sd
                WHERE sd.submission_id = " . $submission_id . "";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();
        return $query->result();
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
                        st.id as state_id,
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

        $result = $query->result();

        foreach ($result as $submission) {
            $submission->totalData = 0;
            $submission->data = $this->getSnapShoot($submission->submission_id, $submission->totalData);
        }

        return $result;
    }

    public function setToProcess($submisiionId, $userid, $organitation_id, &$refTotal)
    {
        $sql = "insert into wfs_history_state (
                        [submission_id],
                        [source_state_id],
                        [destination_state_id],
                        [source_user_id],
                        [destination_user_id],
                        [source_org_id],
                        [destination_org_id],
                        [message],
                        [date_time]
                    )
                select s.id, s.current_state, 102, " . $userid . ", 9, " . $organitation_id . ", 302, '', GETUTCDATE()  from app_submission  s
                WHERE id = " . $submisiionId . "";


        $query = $this->db->query($sql);
        $refTotal = $this->db->affected_rows();

        return $query;
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


/**
 * @OA\Schema(schema="ApplicationInbox")
 */
class ApplicationInbox
{
    /**
     * @OA\Property
     * @var integer
     */
    public $submission_id;  //": 29,
    /**
     * @OA\Property
     * @var string
     */
    public $submission_number;  //": "ORDERIN/TEST/FORMTERBARU/00003",
    /**
     * @OA\Property
     * @var integer
     */
    public $application_id;  //": 201,
    /**
     * @OA\Property
     * @var string
     */
    public $application_code;  //": "SURVEY",
    /**
     * @OA\Property
     * @var string
     */
    public $application_name;  //": "SURVEY",
    /**
     * @OA\Property
     * @var string
     */
    public $submission_date;  //": "2022-06-30 00:27:57.820",
    /**
     * @OA\Property
     * @var float
     */
    public $submission_lat;  //": 0,
    /**
     * @OA\Property
     * @var float
     */
    public $submission_lon;  //": 0,
    /**
     * @OA\Property
     * @var string
     */
    public $state_name;  //": "ASSIGNED",
    /**
     * @OA\Property
     * @var integer
     */
    public $user_id;  //": 9,
    /**
     * @OA\Property
     * @var string
     */
    public $user_name;  //": "DANU PRAKARSA",
    /**
     * @OA\Property
     * @var integer
     */
    public $organitation_id;  //": 302,
    /**
     * @OA\Property
     * @var string
     */
    public $organitaion_name;  //": "CMO",
    /**
     * @OA\Property
     * @var string
     */
    public $submit_message;  //": "INI TEST ORDER IN FORM BARU"

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="object",
     *		  ref="#/components/schemas/ApplicatioSnapShoot"               
     *     )
     *     description="token",
     * ),
     */
    public $data;  //": "INI TEST ORDER IN FORM BARU"
}

/**
 * @OA\Schema(schema="ApplicatioSnapShoot")
 */
class ApplicatioSnapShoot
{
    /**
     * @OA/Property
     * @var integer
     */
    public $id;

    /**
     * @OA/Property
     * @var string
     */
    public $code;

    /**
     * @OA/Property
     * @var string
     */
    public $label;

    /**
     * @OA/Property
     * @var string
     */
    public  $value;
}
