<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Page_model', 'page');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Post(
     *     path="/page/create",
     *     tags={"page"},
     * 	   description="create new page",
     *     @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/PageInput")
     *       ),
     *     ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\JsonContent(
     *        ref="#/components/schemas/PageModel"
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

        $data = $this->page->create($input, $messageResult);

        if ($data != null) {
            $response = $this->responses->successWithData($data, 0);
        } else {
            $response = $this->responses->error($messageResult);
        }

        $this->response($response, 200);
    }

     /**
	 * @OA\Post(
	 *     path="/page/update",
	 *     tags={"page"},
	 * 	   description="update page",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/PageInput")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/PageModel"
	 *      ),
	 *   ),
	 * )
	 */
	public function update_post()
	{
		$id = $this->input->get('id', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->page->update($id, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
	 * @OA\Get(
	 *     path="/page/delete",
	 *     tags={"page"},
	 * 	   description="delete page",
	 *     @OA\Parameter(
	 *       name="id",
	 *       description="id quustion group",
	 *       in="query",
	 * 		 required=true,
	 *       @OA\Schema(type="integer",)
	 *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\JsonContent(     
	 *         ref="#/components/schemas/PageModel"
	 *     ),
	 *   ),
	 * )
	 */
	public function delete_get()
	{
		$id = $this->input->get('id');
		$data = $this->form->delete($id);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

    /**
     * @OA\Get(
     *     path="/page/questions",
     *     tags={"page"},
     * 	   description="Get all quustion group on page",
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
     *				ref="#/components/schemas/PageQuestionDetail"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function questions_get()
    {
        $id = $this->get("id");
		$total = 0;
		$data = null;
		$data = $this->page->getQuestions(["page_id" => $id], $total);
		$response = $this->responses->successWithData($data, $total);
		$this->response($response, 200);
    }

    /**
	 * @OA\Post(
	 *     path="/page/addQuestion",
	 *     tags={"page"},
	 * 	   description="add data question to page",
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/PageQuestion")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/PageQuestionDetail"
	 *      ),
	 *   ),
	 * )
	 */
	public function addQuestion_post()
	{
		$response = null;
		$messageResult = null;
		$data = null;
		$input = json_decode(trim(file_get_contents('php://input')), true);

		$data = $this->page->addQuestion($input, $messageResult);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 0);
		} else {
			$response = $this->responses->error($messageResult);
		}

		$this->response($response, 200);
	}

    /**
	 * @OA\Post(
	 *     path="/page/updateQuestion",
	 *     tags={"page"},
	 * 	   description="update question in page",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="groupId",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/PageQuestion")
	 *       ),
	 *     ),
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/PageQuestionDetail"
	 *      ),
	 *    ),
	 *   )
	 */
	public function updateQuestion_post()
	{
		$id = $this->input->get('id', TRUE);
		$groupId = $this->input->get('groupId', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->page->updateQuestion($id, $groupId, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

     /**
	 * @OA\Post(
	 *     path="/page/deleteQuestion",
	 *     tags={"page"},
	 * 	   description="delete question from page",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="groupId",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/PageQuestionDetail"
	 *      ),
	 *    ),
	 *   )
	 */
	public function deleteQuestion_post()
	{
		$id = $this->input->get('id', true);
		$groupId = $this->input->get('groupId', true);

		$data = $this->page->deleteQuestion($id, $groupId);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}
}
