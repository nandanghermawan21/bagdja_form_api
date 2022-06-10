<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Question extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Question_model', 'question');
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
}
