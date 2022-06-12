<?php

use OpenApi\Annotations\Response;

defined('BASEPATH') or exit('No direct script access allowed');

class Question extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Question_model', 'question');
        $this->load->model('Questiontype_model',"questiontype");
        $this->load->model('Responses_model','responses');
    }

    /**
     * @OA\Get(
     *     path="/question/list",
     *     tags={"question"},
     * 	   description="Get all question list param id null, get specific with param id",
     *     @OA\Parameter(
     *       name="id",
     *       description="id",
     *       in="query",
     *       @OA\Schema(type="integer",default=null)
     *   ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response=200,
     *      description="save location",
     *      @OA\JsonContent(
     *        @OA\Items(ref="#/components/schemas/Question")
     *      ),
     *    ),
     *   ),
     * )
     */
    public function list_get(){
        $id = $this->get("id");
        if($id == null){
            $data = $this->question->get(null);
        }else{
            $data = $this->question->get([$this->question->id => $id]);
        }
        $this->response($data, 200);
    }

     /**
     * @OA\Get(
     *     path="/question/types",
     *     tags={"question"},
     * 	   description="Get all question list param id null, get specific with param id",
     *     @OA\Parameter(
     *       name="id",
     *       description="id",
     *       in="query",
     *       @OA\Schema(type="integer",default=null)
     *   ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response=200,
     *      description="Response data inside Responses model",
     *      @OA\JsonContent(
     *        @OA\Items(ref="#/components/schemas/Questiontype")
     *      ),
     *    ),
     *   ),
     * )
     */
    public function types_get(){
        $id = $this->get("id");
        $total = 0;
        $data = null;
        if($id == null){
            $data = $this->questiontype->get(null, $total);
        }else{
            $data = $this->questiontype->get([$this->question->id => $id], $total);
        }
        $response = $this->responses->successWithData($data,$total);
        $this->response($response, 200);
    }
}
