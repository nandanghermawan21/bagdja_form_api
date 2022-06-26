<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Dudung extends MY_Controller
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
     *     path="/dudung/listQuestionState",
     *     tags={"dudung"},
     * 	   description="Get all question on state apllication",
     *     @OA\Parameter(
     *       name="code",
     *       description="application code",
     *       in="query",
     *       @OA\Schema(type="string",default="SURVEY")
     *     ),'
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
        $stateId = $this->get("id");
        $total = 0;
        $data = null;
        $data = $this->application->getQuestionState($applicationCode, $stateId, $total);
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }
}
