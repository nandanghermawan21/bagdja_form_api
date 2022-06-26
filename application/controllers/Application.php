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
     *       required=true,
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="cmoUserName",
     *       description="CMO userName",
     *       in="query",
     *       required=true,
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="cmoFullName",
     *       description="CMO fullName",
     *       in="query",
     *       required=true,
     *       @OA\Schema(type="string",default="")
     *     ),
     *     @OA\Parameter(
     *       name="message",
     *       description="message",
     *       in="query",
     *       required=true,
     *       @OA\Schema(type="string",default="")
     *     ),
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/QuestionValueInput"               
     *             )
     *         ),
	 *       ),
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
    public function assignSurvey_post(){
        $data = $this->_getData();
        print_r($data);
    }
}
