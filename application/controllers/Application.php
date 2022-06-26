<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Application extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

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
        //validate 
        $input = [
            'orderNumber' => $this->input->get("orderNumber", TRUE),
            'cmoUserName' => $this->input->get("cmoUserName", TRUE),
            'cmoFullName' => $this->input->get("cmoFullName", TRUE),
            'message' => $this->input->get("message", TRUE),
        ];

        print_r($input);

        //read body
        $body = json_decode(trim(file_get_contents('php://input')), true);

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
            $this->response($body, 200);
        }
    }
}
