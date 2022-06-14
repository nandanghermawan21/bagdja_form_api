<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Collection extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->_authenticate();

		$this->load->model('Collection_model', 'collection');
		$this->load->model('Responses_model', 'responses');
	}


	/**
	 * @OA\Get(
	 *     path="/collection/list",
	 *     tags={"collection"},
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
	 *				ref="#/components/schemas/CollectionModel"               
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
			$data = $this->collection->get(null, $total);
		} else {
			$data = $this->collection->get(["id" => $id], $total);
		}
		$response = $this->responses->successWithData($data, $total);
		$this->response($response, 200);
	}

	/**
	 * @OA\Post(
	 *     path="/collection/create",
	 *     tags={"collection"},
	 * 	   description="create new collection list",
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/CollectionInput")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/CollectionModel"
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

		$data = $this->collection->create($input, $messageResult);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 0);
		} else {
			$response = $this->responses->error($messageResult);
		}

		$this->response($response, 200);
	}


	/**
	 * @OA\Post(
	 *     path="/collection/update",
	 *     tags={"collection"},
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
	 *         @OA\Schema(ref="#/components/schemas/CollectionInput")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/CollectionModel"
	 *      ),
	 *   ),
	 * )
	 */
	public function update_post()
	{
		$id = $this->get("id");
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->collection->update($id, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData(null, $data);
		} else {
			$response = $this->responses->error("Id cannot be null");
		}
		$this->response($response, 200);
	}


	/**
	 * @OA\Get(
	 *     path="/collection/list/delete",
	 *     tags={"collection"},
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
	 *         @OA\Property(property="id", type="integer", default=0),
	 *     ),
	 *   ),
	 * )
	 */
	public function _remove()
	{
		$id = $this->input->get('id');
		$da = $this->collection->delete(['id' => $id], 'sys_collection');

		if ($da > 0) {
			$data = ['id' => $id];
			$this->response($data, 200);
		} else {
			$data = [
				'success' => false,
				'message' => 'delete collection invalid data',
			];
			$this->response($data, 400);
		}
	}

	// data

	public function data_post()
	{
		$uri = $this->uri->segment(3);

		switch ($uri) {
			case 'create':
				$this->create();
				break;
			case 'update':
				$this->update();
				break;
			default:
				show_404();
				break;
		}
	}

	public function data_get()
	{
		$uri = $this->uri->segment(3);

		switch ($uri) {
			case null:
			case false:
			case '':
				$this->show();
				break;
			case 'delete':
				$this->remove();
				break;
			default:
				show_404();
				break;
		}
	}

	/**
	 * @OA\Get(
	 *     path="/collection/data",
	 *     tags={"collection"},
	 * 	   description="Get all collection data param id null, get specific with param id",
	 *     @OA\Parameter(
	 *       name="id",
	 *       description="id list data",
	 *       in="query",
	 *       @OA\Schema(type="integer",default=null)
	 *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(type="array",
	 *             @OA\Items(type="object",
	 *				ref="#/components/schemas/CollectionModel"               
	 *             )
	 *         ),
	 *     ),
	 *   ),
	 * )
	 */

	public function show()
	{
		$id = $this->input->get('id');
		if ($id == null) {
			$da = $this->collection->view(null, 'sys_collection_data');
		} else {
			$da = $this->mdata->view(['id' => $id], 'sys_collection_data');
		}
		$this->response($da, 200);
	}

	/**
	 * @OA\Post(
	 *     path="/collection/data/create",
	 *     tags={"collection"},
	 *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
	 *		ref="#/components/schemas/collectionInputData"
	 *     ),
	 * ),
	 *    @OA\Response(response="400", description="required field",
	 *       @OA\JsonContent(
	 *       ref="#/components/schemas/required"
	 *     ),
	 * ),
	 *    @OA\RequestBody(
	 *      required=true,
	 *      @OA\JsonContent(
	 *		ref="#/components/schemas/collectionInputData"
	 *     ),
	 *   ),
	 *   security={{"bearerAuth": {}}},
	 * )
	 */

	public function create()
	{
		$val = [
			'collection_id' => 'collection_id',
			'value' => 'value',
			'label' => 'label'
		];

		$input = json_decode(trim(file_get_contents('php://input')), true);
		if ($input) {
			$this->form_validation->set_data($input);
			foreach ($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);

			if ($this->form_validation->run() == FALSE) {
				foreach ($val as $key => $value) {
					$data['messages'][$key] = form_error($key);
				}
				$this->response($data, 400);
			} else {
				$this->collection->insert($input, 'sys_collection_data');
				$this->response($input, 200);
			}
		}
	}


	/**
	 * @OA\Post(
	 *     path="/collection/data/update",
	 *     tags={"collection"},
	 *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
	 *		ref="#/components/schemas/collectionInputData"
	 *     ),
	 * ),
	 *    @OA\Response(response="400", description="required field",
	 *       @OA\JsonContent(
	 *       ref="#/components/schemas/required"
	 *     ),
	 * ),
	 *    @OA\RequestBody(
	 *      required=true,
	 *      @OA\JsonContent(
	 *		ref="#/components/schemas/collectionInputData"
	 *     ),
	 *   ),
	 *   security={{"bearerAuth": {}}},
	 * )
	 */

	public function update()
	{

		$val = [
			'collection_id' => 'collection id',
			'value' => 'value',
			'label' => 'label'
		];

		$data = array('success' => false, 'messages' => array());
		$input = json_decode(trim(file_get_contents('php://input')), true);
		if ($input) {
			$this->form_validation->set_data($input);
			foreach ($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);

			if ($this->form_validation->run() == FALSE) {
				foreach ($val as $key => $value) {
					$data['messages'][$key] = form_error($key);
				}
				http_response_code('400');
			} else {

				$wh = ['collection_id' => $input['collection_id']];

				$cc = $this->mdata->update_all($wh, $input, 'sys_collection_data');

				if ($cc > 0) {
					$data = [
						'success' => true,
						'message' => 'update collection success'
					];
					http_response_code('200');
				} else {
					$data = [
						'success' => false,
						'message' => "update collection failed"
					];
					http_response_code('400');
				}
			}
			echo json_encode($data);
		}
	}

	/**
	 * @OA\Get(
	 *     path="/collection/data/delete",
	 *     tags={"collection"},
	 * 	   description="Get all collection data param id null, get specific with param id",
	 *     @OA\Parameter(
	 *       name="id",
	 * 		 required=true,
	 *       description="id collection data",
	 *       in="query",
	 *       @OA\Schema(type="integer",default=null)
	 *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\JsonContent(     
	 *         @OA\Property(property="collection_id", type="integer", default=0),
	 *     ),
	 *   ),
	 * )
	 */
	public function remove()
	{
		$id = $this->input->get('id');
		$da = $this->collection->delete(['collection_id' => $id], 'sys_collection_data');

		if ($da > 0) {
			$data = ['collection_id' => $id];
			$this->response($data, 200);
		} else {
			$data = [
				'success' => false,
				'message' => 'delete collection invalid data',
			];
			$this->response($data, 400);
		}
	}
}
