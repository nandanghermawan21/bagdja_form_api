<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Questiongroup extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Questiongroup_model', 'questiongroup');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/questiongroup/list",
     *     tags={"Question Group"},
     * 	   description="Get all collection list param id null, get specific with param id",
     *     @OA\Parameter(
     *       name="id",
     *       description="id list",
     *       in="query",
     *       @OA\Schema(type="integer",default=null)
     *   ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/QuestionGroupModel"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function list_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        if ($id == null) {
            $data = $this->questiongroup->get(null, $total);
        } else {
            $data = $this->questiongroup->get(["id" => $id], $total);
        }
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/questiongroup/create",
     *     tags={"Question Group"},
     * 	   description="create new question group",
     *     @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/QuestionGroupInput")
     *       ),
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\JsonContent(
     *        ref="#/components/schemas/QuestionGroupModel"
     *      ),
     *   ),
     * )
     */
    public function create_post()
    {

        $response = null;
        $messageResult = null;
        $data = null;
        $input = json_decode(trim(file_get_contents('php://input')), true);

        $data = $this->questiongroup->create($input, $messageResult);

        if ($data != null) {
            $response = $this->responses->successWithData($data, 0);
        } else {
            $response = $this->responses->error($messageResult);
        }

        $this->response($response, 200);
    }

    /**
	 * @OA\Post(
	 *     path="/questiongroup/update",
	 *     tags={"Question Group"},
	 * 	   description="update group",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/QuestionGroupInput")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/QuestionGroupModel"
	 *      ),
	 *   ),
	 * )
	 */
	public function update_post()
	{
		$id = $this->input->get('id', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->questiongroup->update($id, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
	 * @OA\Get(
	 *     path="/questiongroup/delete",
	 *     tags={"Question Group"},
	 * 	   description="Get all collection list param id null, get specific with param id",
	 *     @OA\Parameter(
	 *       name="id",
	 *       description="id collection list",
	 *       in="query",
	 * 		 required=true,
	 *       @OA\Schema(type="integer",)
	 *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\JsonContent(     
	 *         ref="#/components/schemas/QuestionGroupModel"
	 *     ),
	 *   ),
	 * )
	 */
	public function delete_get()
	{
		$id = $this->input->get('id');
		$data = $this->questiongroup->delete($id);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
	 * @OA\Get(
	 *     path="/questiongroup/data",
	 *     tags={"Question Group"},
	 * 	   description="get data collection",
	 *     @OA\Parameter(
	 *       name="id",
	 *       description="id collection",
	 *       in="query",
	 *       required=true,
	 *       @OA\Schema(type="integer",default=null)
	 *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(type="array",
	 *             @OA\Items(type="object",
	 *				ref="#/components/schemas/QuestionGroupModel"               
	 *             )
	 *         ),
	 *     ),
	 *   ),
	 * )
	 */
	public function data_get()
	{
		$id = $this->get("id");
		$total = 0;
		$data = null;
		$data = $this->questiongroup->getData(["group_id" => $id], $total);
		$response = $this->responses->successWithData($data, $total);
		$this->response($response, 200);
	}

    /**
	 * @OA\Post(
	 *     path="/questiongroup/addData",
	 *     tags={"Question Group"},
	 * 	   description="add data question to question group",
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/QuestionGroupData")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/QuestionGroupData"
	 *      ),
	 *   ),
	 * )
	 */
	public function addData_post()
	{
		$response = null;
		$messageResult = null;
		$data = null;
		$input = json_decode(trim(file_get_contents('php://input')), true);

		$data = $this->questiongroup->addData($input, $messageResult);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 0);
		} else {
			$response = $this->responses->error($messageResult);
		}

		$this->response($response, 200);
	}

    /**
	 * @OA\Post(
	 *     path="/questiongroup/updateData",
	 *     tags={"Question Group"},
	 * 	   description="update collection",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="questionId",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/QuestionGroupData")
	 *       ),
	 *     ),
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/QuestionGroupData"
	 *      ),
	 *    ),
	 *   )
	 */
	public function updateData_post()
	{
		$id = $this->input->get('id', TRUE);
		$questionId = $this->input->get('questionId', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->questiongroup->updateData($id, $questionId, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
	 * @OA\Post(
	 *     path="/questiongroup/deleteData",
	 *     tags={"Question Group"},
	 * 	   description="update collection",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="questionId",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="String",default=null)
	 *     ),	 
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/QuestionGroupData"
	 *      ),
	 *    ),
	 *   )
	 */
	public function deleteData_post()
	{
		$id = $this->input->get('id');
		$value = $this->input->get('value');
		$data = $this->questiongroup->deleteData($id, $value);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}
}
