<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Application extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();


        $this->load->model('Auth_model', 'auth');
        $this->load->model('Application_model', 'application');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/application/listQuestionState",
     *     tags={"Application"},
     * 	   description="Get all question on state apllication",
     *     @OA\Parameter(
     *       name="code",
     *       description="application code",
     *       in="query",
     *       @OA\Schema(type="string",default="SURVEY")
     *     ),
     *     @OA\Parameter(
     *       name="stateId",
     *       description="application state",
     *       in="query",
     *       @OA\Schema(type="integer",default="100")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/QuestionState"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function listQuestionState_get()
    {
        $applicationCode = $this->get("code");
        $stateId = $this->get("stateId");
        $total = 0;
        $data = null;
        $data = $this->application->getQuestionState($applicationCode, $stateId, $total);
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\POST(
     *     path="/application/assignSurvey",
     *     tags={"Application"},
     * 	   description="automaticaky create submission and assign survey to CMO user",
     *     @OA\Parameter(
     *       name="orderNumber",
     *       description="Order Number",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="cmoUserName",
     *       description="CMO userName",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="cmoFullName",
     *       description="CMO fullName",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="message",
     *       description="message",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="lat",
     *       description="lat",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="lon",
     *       description="lon",
     *       in="query",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="deviceId",
     *       description="deviceId",
     *       in="header",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="deviceModel",
     *       description="deviceModel",
     *       in="header",
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(type="array",
     *               @OA\Items(type="object",
     *		     		ref="#/components/schemas/QuestionValueInput"               
     *               )
     *           ),
     *         ),
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/QuestionState"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function assignSurvey_post()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        //deviceInfo
        $deviceInfo = $this->_getDeviceInfo();

        //input 
        $input = [
            'orderNumber' => $this->input->get("orderNumber", TRUE),
            'cmoUserName' => $this->input->get("cmoUserName", TRUE),
            'cmoFullName' => $this->input->get("cmoFullName", TRUE),
            'message' => $this->input->get("message", TRUE),
            'lat' => $this->input->get("lat", TRUE),
            'lon' => $this->input->get("lon", TRUE),
        ];

        //read body
        $data = json_decode(trim(file_get_contents('php://input')), true);

        $this->form_validation->set_data($input);

        foreach ($input as $row => $key) :
            $this->form_validation->set_rules($row, $row, 'trim|required|xss_clean');
        endforeach;

        $this->form_validation->set_error_delimiters(null, null);

        if ($this->form_validation->run() == FALSE) {
            $message = array();
            foreach ($input as $key => $value) {
                $message[$key] = form_error($key);
            }
            $this->response($message, 403);
        } else {

            $resultMessage = "";
            $result = $this->application->assignSurvey($user, $input, $data, $deviceInfo, $resultMessage);

            if ($result == null) {
                $this->response($this->responses->error($resultMessage), 403);
            } else {
                $this->response($this->responses->successWithData(["submission_id" => $result]), 200);
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/application/inbox",
     *     tags={"Application"},
     * 	   description="Get all question on state apllication",
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/ApplicationInbox"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function inbox_get()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        $total = 0;
        $data = null;
        $data = $this->application->getInbox($user->id, $total);
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/application/formRef",
     *     tags={"Application"},
     * 	   description="Get all form ref read only",
     *     @OA\Parameter(
     *       name="submissionId",
     *       description="Submission ID",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/ApplicationInbox"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function formRef_get()
    {
        //getuserInfo
        $submissionId = $this->input->get("submissionId", TRUE);

        $total = 0;
        $data = null;
        $data = $this->application->getFormRef($submissionId, $total);
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }


    /**
     * @OA\Get(
     *     path="/application/form",
     *     tags={"Application"},
     * 	   description="Get all form ref read only",
     *     @OA\Parameter(
     *       name="submissionId",
     *       description="Submission ID",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\JsonContent(
     *        ref="#/components/schemas/ApplicationInbox"
     *      ),
     *   ),
     * )
     */
    public function form_get()
    {
        //getuserInfo
        $submissionId = $this->input->get("submissionId", TRUE);

        $total = 0;
        $data = null;
        $data = $this->application->getForm($submissionId, $total);
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/application/confirmRead",
     *     tags={"Application"},
     * 	   description="set submission to readed (getted to local storage device on app)",
     *     @OA\Parameter(
     *       name="submissionId",
     *       description="Submission ID",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\bool,
     *   ),
     * )
     */
    public function confirmRead_get()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        //deviceInfo
        $deviceInfo = $this->_getDeviceInfo();

        //getuserParam
        $submissionId = $this->input->get("submissionId", TRUE);

        $total = 0;
        $resultMessage = "";
        $data = null;
        $data = $this->application->confirm($submissionId, $user->id, 'READED', '', $deviceInfo, $total, $resultMessage);
        if ($data == null) {
            $this->response($this->responses->error($resultMessage), 403);
        } else {
            $this->response($this->responses->successWithData($data, $total), 200);
        }
    }


    /**
     * @OA\Get(
     *     path="/application/confirmProcess",
     *     tags={"Application"},
     * 	   description="set submission to process (getted to local storage device on app)",
     *     @OA\Parameter(
     *       name="submissionId",
     *       description="Submission ID",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\bool,
     *   ),
     * )
     */
    public function confirmProcess_get()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        //deviceInfo
        $deviceInfo = $this->_getDeviceInfo();

        //getuserParam
        $submissionId = $this->input->get("submissionId", TRUE);

        $total = 0;
        $resultMessage = "";
        $data = null;
        $data = $this->application->confirm($submissionId, $user->id, 'PROCESSED', '', $deviceInfo, $total, $resultMessage);

        if ($data == null) {
            $this->response($this->responses->error($resultMessage), 403);
        } else {
            $this->response($this->responses->successWithData($data, $total), 200);
        }
    }


    /**
     * @OA\GET(
     *     path="/application/confirmUploading",
     *     tags={"Application"},
     * 	   description="set submission to uploading process (getted to local storage device on app)",
     *     @OA\Parameter(
     *       name="submissionId",
     *       description="Submission ID",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     *     @OA\Parameter(
     *       name="message",
     *       description="message",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\bool,
     *   ),
     * )
     */
    public function confirmUploading_get()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        //deviceInfo
        $deviceInfo = $this->_getDeviceInfo();

        //getuserParam
        $submissionId = $this->input->get("submissionId", TRUE);
        $message = $this->input->get("message", TRUE);

        //getSubmision
        $totalSubmission = 0;
        $submission = $this->application->getSubmission(["id" => $submissionId], $totalSubmission);

        if ($totalSubmission == 0) {
            $this->response($this->responses->error("submission not found"), 403);
        }

        if($submission->current_device_id != $deviceInfo->deviceId){
            $this->response($this->responses->error("in progess at ".$deviceInfo->deviceModel), 403);
        }

        $total = 0;
        $resultMessage = "";
        $data = null;
        $data = $this->application->confirm($submissionId, $user->id, 'UPLOADING', $message, $deviceInfo, $total, $resultMessage);
        if ($data == null) {
            $this->response($this->responses->error($resultMessage), 403);
        } else {
            $this->response($this->responses->successWithData($data, $total), 200);
        }
    }

    /**
     * @OA\GET(
     *     path="/application/upload",
     *     tags={"Application"},
     * 	   description="upload onw by one question",
     *     @OA\Parameter(
     *       name="message",
     *       description="message",
     *       in="query",
     *       @OA\Schema(type="integer",default="")
     *     ),
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(type="array",
     *               ref="#/components/schemas/SubmissionDataUpload" 
     *           ),
     *         ),
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\bool,
     *   ),
     * )
     */
    public function upload_post()
    {
        //getuserInfo
        $user = $this->_getData()->data;

        //deviceInfo
        $deviceInfo = $this->_getDeviceInfo();

        //getuserParam
        $message = $this->input->get("message", TRUE);

        $body = json_decode(trim(file_get_contents('php://input')), true);


        //getSubmision
        $totalSubmission = 0;
        $submission = $this->application->getSubmission(["id" => $body->submission_id], $totalSubmission);

        //check device
        if ($totalSubmission == 0) {
            $this->response($this->responses->error("submission not found"), 403);
        }

        if($submission->current_device_id != $deviceInfo->deviceId){
            $this->response($this->responses->error("in progess at ".$deviceInfo->deviceModel), 403);
        }

        //upload
        $totalUpload = 0;
        $resultMessageUpload = "";
        $resultUpload = $this->application->upload($user,$body,$deviceInfo,$totalUpload,$resultMessageUpload);

        if($resultUpload == false){
            $this->response($this->responses->error($resultMessageUpload), 403);
        }

        $total = 0;
        $resultMessage = "";
        $data = null;
        $data = $this->application->confirm($body->submission_id, $user->id, 'UPLOADED', $message, $deviceInfo, $total, $resultMessage);
        if ($data == null) {
            $this->response($this->responses->error($resultMessage), 403);
        } else {
            $this->response($this->responses->successWithData($data, $total), 200);
        }
    }
}
