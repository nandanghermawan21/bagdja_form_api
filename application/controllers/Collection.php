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
		$id = $this->input->get('id', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->collection->update($id, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}


	/**
	 * @OA\Get(
	 *     path="/collection/delete",
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
	public function delete_get()
	{
		$id = $this->input->get('id');
		$data = $this->collection->delete($id);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
	}

	/**
	 * @OA\Get(
	 *     path="/collection/data",
	 *     tags={"collection"},
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
	 *				ref="#/components/schemas/CollectionData"               
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
		$data = $this->collection->getData(["collection_id" => $id], $total);
		$response = $this->responses->successWithData($data, $total);
		$this->response($response, 200);
	}

	/**
	 * @OA\Post(
	 *     path="/collection/addData",
	 *     tags={"collection"},
	 * 	   description="create new collection list",
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/CollectionData")
	 *       ),
	 *     ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/CollectionData"
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

		$data = $this->collection->addData($input, $messageResult);

		if ($data != null) {
			$response = $this->responses->successWithData($data, 0);
		} else {
			$response = $this->responses->error($messageResult);
		}

		$this->response($response, 200);
	}


	/**
	 * @OA\Post(
	 *     path="/collection/updateData",
	 *     tags={"collection"},
	 * 	   description="update collection",
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="id",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="String",default=null)
	 *     ),	 
	 *     @OA\RequestBody(
	 *     @OA\MediaType(
	 *         mediaType="application/json",
	 *         @OA\Schema(ref="#/components/schemas/CollectionInput")
	 *       ),
	 *     ),
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/CollectionModel"
	 *      ),
	 *    ),
	 *   )
	 */
	public function updateData_post()
	{
		$id = $this->input->get('id', TRUE);
		$value = $this->input->get('value', TRUE);
		$message = "";
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$data = $this->collection->updateData($id, $value, $input, $message);
		if ($data != null) {
			$response = $this->responses->successWithData($data, 1);
		} else {
			$response = $this->responses->error("id not found");
		}
		$this->response($response, 200);
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
