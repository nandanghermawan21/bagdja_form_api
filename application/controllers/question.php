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
        $this->load->model('Questiontype_model', "questiontype");
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/question/list",
     *     tags={"Question"},
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
    public function list_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        if ($id == null) {
            $data = $this->question->get(null, $total);
        } else {
            $data = $this->question->get(["id" => $id], $total);
        }
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/question/types",
     *     tags={"Question"},
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
    public function types_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        if ($id == null) {
            $data = $this->questiontype->get(null, $total);
        } else {
            $data = $this->questiontype->get([$this->question->id => $id], $total);
        }
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/question/add",
     *     tags={"Question"},
     * 	   description="add question",
     *     @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/QuestionInput")
     *       ),
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response=200,
     *      description="Response data inside Responses model",
     *      @OA\JsonContent(
     *        ref="#/components/schemas/Question"
     *      ),
     *    ),
     *   ),
     * )
     */
    public function add_post()
    {
        $response = null;
        $messageResult = null;
        $data = null;
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->form_validation->set_data($input);

        $data = $this->question->add($input, $messageResult);

        if ($data != null) {
            $response = $this->responses->successWithData($data, 0);
        } else {
            $response = $this->responses->error($messageResult);
        }

        $this->response($response, 200);
    }

    /**
	 * @OA\Post(
	 *     path="/question/update",
	 *     tags={"Question"},
	 * 	   description="update collection",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/QuestionInput")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/Question"
	 *      ),
	 *   ),
	 * )
	 */
	public function update_post()
	{
		$id = $this->input->get('id', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->question->update($id, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
     * @OA\Get(
     *     path="/question/delete",
     *     tags={"Question"},
     * 	   description="delete question by id",
     *     @OA\Parameter(
     *       name="id",
     *       description="id",
     *       in="query",
     * 		 required=true,
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
    public function delete_get()
    {
        $id = $this->get("id");
        $data = null;
        $response = null;
        if ($id == null) {
            $response = $this->responses->error("Id not found");
        } else {
            $data = $this->question->delete($id);
            $response = $this->responses->successWithData($data, 1);
        }
        $this->response($response, 200);
    }
}
