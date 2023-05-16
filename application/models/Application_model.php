<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

    public function getSubmission($where, &$refTotal)
    {
        if ($where != null) {
            $this->db->where($where);
        }
        $query = $this->db->get("app_submission");
        $refTotal = $query->num_rows();
        return $query->result();
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

        return $this->getFormComponent($submission_id, $result);
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

        return $this->getFormComponent($submission_id, $result)[0];
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
                        $question->submission_id = (int) $submission_id;
                        $question->form_id = $form->id;
                        $question->page_id = $page->id;
                    }
                }
            }
        }

        return $forms;
    }

    //cuntion khusus merecord order ke submission and set current user to CMO
    public function assignSurvey($user, $submission, $data, $deviceInfo, &$message)
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
            [current_device_model],
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
            " . $cek->id . ",
            101,
            " . $submission["lat"] . ",
            " . $submission["lon"] . ",
            'SUBMITED',
            '" . $submission["message"] . "',
            '" . $deviceInfo->deviceId . "',
            '" . $deviceInfo->deviceModel . "',
            " . $user->id . ",
            GETUTCDATE()
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
            [device_model]
        )VALUES(
            " . $submissionId . ",
            100,
            101,
            " . $user->id . ",
            " . $cek->id . ",
            " . $user->organitation_id . ",
            302,
            '" . $submission["message"] . "',
            GETUTCDATE(),
            'SUBMITED',
            " . $user->id . ",
            '" . $deviceInfo->deviceId . "',
            '" . $deviceInfo->deviceModel . "'
        );";

        $this->db->query($insertHistory);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $message = "assign submission surver failed";
            return null;
        } else {
            return $submissionId;
        }
    }

    public function getSubmissionData($submission_id, &$refTotal)
    {
        $sql = "select submission_id,
                    question_id,
                    CASE WHEN q.[type] = 'foto' THEN '[foto]' ELSE [value] END as 'value',
                    lat,
                    lon
                from app_submission_data sd
                    join sys_question q on sd.question_id = q.id
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
                        s.current_state_status as state_status,
                        u.id as user_id,
                        u.name as user_name,
                        o.id as organitation_id,
                        o.name as organitaion_name,
                        s.current_device_id as device_id,
                        s.current_device_model as device_model,
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
                    WHERE s.current_state_status in ('SUBMITED', 'READED', 'PROCESSED') and
                    s.current_state = 101 and
                    s.current_user_id = " . $userid . "";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();

        $result = $query->result();

        foreach ($result as $submission) {
            $submission->totalData = 0;
            $submission->data = $this->getSnapShoot($submission->submission_id, $submission->totalData);
        }

        return $result;
    }

    public function getFinished($userid, &$refTotal)
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
                        s.current_state_status as state_status,
                        u.id as user_id,
                        u.name as user_name,
                        o.id as organitation_id,
                        o.name as organitaion_name,
                        s.current_device_id as device_id,
                        s.current_device_model as device_model,
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
                    WHERE s.current_state = 102 and
                    s.current_user_id = " . $userid . "";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();

        $result = $query->result();

        foreach ($result as $submission) {
            $submission->totalData = 0;
            $submission->data = $this->getSnapShoot($submission->submission_id, $submission->totalData);
        }

        return $result;
    }

    public function confirm($submisiionId, $userid, $status, $message, $deviceInfo, &$refTotal, &$resultMessage)
    {
        //start transaction
        $this->db->trans_start();

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
                        [device_model]
                    )
                select s.id, 
                       s.current_state, 
                       s.current_state, 
                       s.current_user_id, 
                       s.current_user_id, 
                       s.current_organitation_id, 
                       s.current_organitation_id, 
                       '" . $message . "',
                       GETUTCDATE(), 
                       '" . $status . "', 
                       " . $userid . ",
                       '" . $deviceInfo->deviceId . "',
                       '" . $deviceInfo->deviceModel . "'
                from app_submission  s
                WHERE id = " . $submisiionId . "";
        $query = $this->db->query($insertHistory);
        $refTotal = $this->db->affected_rows();

        $updateSubmission = "UPDATE app_submission
                                set current_state_status = '" . $status . "',
                                    current_state_message = '" . $message . "',
                                    current_device_id = '" . $deviceInfo->deviceId . "',
                                    current_device_model = '" . $deviceInfo->deviceModel . "',
                                    updated_by = " . $userid . ",
                                    updated_date = GETUTCDATE()
                                where id = " . $submisiionId . "";

        $this->db->query($updateSubmission);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $resultMessage = "confirm " . $status . " failed";
            return false;
        } else {
            return true;
        }
    }

    public function upload($user, $data, $deviceInfo, &$refTotal, &$resultMessage)
    {

        //start transaction
        $this->db->trans_start();

        $insertQuestionHisotry = "INSERT into   app_submission_data_history(
                                                [submission_id],
                                                [form_id],
                                                [page_id],
                                                [group_id],
                                                [question_id],
                                                [value],
                                                [lat],
                                                [lon],
                                                [input_date],
                                                [device_id],
                                                [device_model],
                                                [created_by],
                                                [created_date]
                                            )VALUES(
                                                " . $data["submission_id"] . ",
                                                " . $data["form_id"] . ",
                                                " . $data["page_id"] . ",
                                                " . $data["group_id"] . ",
                                                " . $data["question_id"] . ",
                                                '" . $data["value"] . "',
                                                " . $data["lat"] . ",
                                                " . $data["lon"] . ",
                                                '" . $data["input_date"] . "',
                                                '" . $deviceInfo->deviceId . "',
                                                '" . $deviceInfo->deviceModel . "',
                                                " . $user->id . ",
                                                GETUTCDATE()
                                            )";

        $this->db->query($insertQuestionHisotry);
        $refTotal = $this->db->affected_rows();

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $resultMessage = "upload failed";
            return false;
        } else {
            return true;
        }
    }

    public function changeState($submissionId, $user, $deviceInfo, $message, $stateId, &$resultMessage)
    {
        $this->db->trans_start();

        $queryUpdateState = "update app_submission
                                set prev_state = current_state,
                                    prev_organitation_id = current_organitation_id,
                                    prev_user_id = current_user_id,
                                    current_state = " . $stateId . ",
                                    current_organitation_id = " . $user->organitation_id . ",
                                    current_user_id = " . $user->id . ",
                                    current_state_message = '" . $message . "',
                                    updated_by = " . $user->id . ",
                                    updated_date = GETUTCDATE()
                                where id = " . $submissionId . "";

        $this->db->query($queryUpdateState);

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
                            [device_model]
                        )
                        select s.id, 
                            s.prev_state, 
                            s.current_state,
                            s.prev_user_id,
                            s.current_user_id,
                            s.prev_organitation_id,
                            s.current_organitation_id,
                            s.current_state_message,
                            GETUTCDATE(),
                            'SUBMITED',
                             " . $user->id . ",
                            '" . $deviceInfo->deviceId . "',
                            '" . $deviceInfo->deviceModel . "'
                        from app_submission s
                        where s.id = " . $submissionId . "";

        $this->db->query($insertHistory);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $resultMessage = "upload failed";
            return false;
        } else {
            return true;
        }
    }

    public function getValue($submissionId, $questionid, &$refTotal)
    {
        $sql = "select submission_id,
                    question_id,
                    [value],
                    lat,
                    lon
                from app_submission_data sd
                    join sys_question q on sd.question_id = q.id
                WHERE sd.submission_id = " . $submissionId . " and question_id = " . $questionid . "";

        $query = $this->db->query($sql);

        $refTotal = $query->num_rows();
        return $query->result();
    }

    public function getInitUploadCount($submissionId)
    {
        $sql = "select * from 
                         wfs_history_state s
                         where s.submission_id = " . $submissionId . "
                         and s.[message] like 'Init Upload % question'
                         ORDER By date_time DESC";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $pieces = explode(" ", $row->message);
            return intval($pieces[2]);
        } else {
            return 0;
        }
    }

    public function validateUploaded($submissionId, $totalUpload)
    {
        $sql = "select * from 
                         wfs_history_state s
                         where s.submission_id =  " . $submissionId . "
                         and s.[message] = '" . $totalUpload . "/" . $totalUpload . "'
                         ORDER By date_time DESC";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
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


/**
 * @OA\Schema(schema="ApplicationInbox")
 */
class ApplicationInbox
{
    /**
     * @OA\Property
     * @var integer
     */
    public $submission_id; //": 29,
    /**
     * @OA\Property
     * @var string
     */
    public $submission_number; //": "ORDERIN/TEST/FORMTERBARU/00003",
    /**
     * @OA\Property
     * @var integer
     */
    public $application_id; //": 201,
    /**
     * @OA\Property
     * @var string
     */
    public $application_code; //": "SURVEY",
    /**
     * @OA\Property
     * @var string
     */
    public $application_name; //": "SURVEY",
    /**
     * @OA\Property
     * @var string
     */
    public $submission_date; //": "2022-06-30 00:27:57.820",
    /**
     * @OA\Property
     * @var float
     */
    public $submission_lat; //": 0,
    /**
     * @OA\Property
     * @var float
     */
    public $submission_lon; //": 0,
    /**
     * @OA\Property
     * @var string
     */
    public $state_name; //": "ASSIGNED",
    /**
     * @OA\Property
     * @var integer
     */
    public $user_id; //": 9,
    /**
     * @OA\Property
     * @var string
     */
    public $user_name; //": "DANU PRAKARSA",
    /**
     * @OA\Property
     * @var integer
     */
    public $organitation_id; //": 302,
    /**
     * @OA\Property
     * @var string
     */
    public $organitaion_name; //": "CMO",
    /**
     * @OA\Property
     * @var string
     */
    public $submit_message; //": "INI TEST ORDER IN FORM BARU"

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="object",
     *		  ref="#/components/schemas/ApplicatioSnapShoot"               
     *     )
     *     description="token",
     * ),
     */
    public $data; //": "INI TEST ORDER IN FORM BARU"
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
    public $value;
}

/**
 * @OA\Schema(schema="SubmissionDataUpload")
 */
class SubmissionDataUpload
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $question_id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $value;
    /**
     * @OA\Property()
     * @var double
     */
    public $lat;
    /**
     * @OA\Property()
     * @var double
     */
    public $lon;
    /**
     * @OA\Property()
     * @var integer
     */
    public $form_id;
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
    public $submission_id;
    /**
     * @OA\Property()
     * @var string
     */
    public $input_date;
}