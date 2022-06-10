<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Organitation extends MY_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->_authenticate();		

		$this->load->model('Organitation_model', 'organitation');
    }

	/**
     * @OA\Get(
     *     path="/organitation",
     *     tags={"organitation"},
	 * 	   description="Get all organitation param id null, get specific with param id",
	 *     @OA\Parameter(
     *       name="id",
	 *       description="id organitation",
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
	 *				ref="#/components/schemas/OrganitationModel"               
     *             )
     *         ),
     *     ),
     *   ),
	 * )
     */



	public function index_get()
	{		
		$id = $this->input->get('id');
		if($id == null)
		{
			$da = $this->organitation->view(null);
		}
		else
		{
			$da = $this->organitation->view(['id'=>$id]);
		}

		$this->response($da,200);
	}

	/**
     * @OA\Get(
     *     path="/organitation/tree",
     *     tags={"organitation"},
	 * 	   description="Get all organitation param id null, get specific with param id",
	 *     @OA\Parameter(
     *       name="id",
	 *       description="id organitation",
	 *       required=true,
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
	 *				ref="#/components/schemas/OrganitationTree"               
     *             )
     *         ),
     *     ),
     *   ),
	 * )
     */

	public function tree_get()
	{				
		$id = $this->input->get('id');
		$wh = ['usm_organitation_level.parent_id'=>$id];
		if($id == null)
		{
			$da = $this->organitation->tree(0);
		}
		else
		{
			$da = $this->organitation->tree($wh);
		}
		$this->response($da, 200);		
	}

	  /**
     * @OA\Post(
     *     path="/organitation/create",
     *     tags={"organitation"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/OrganitationModel"
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
     *		ref="#/components/schemas/OrganitationModel"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function create_post()
	{
		$val = [
			'name' => 'name',
			'code' => 'code'
		];

		
		$input = json_decode(trim(file_get_contents('php://input')), true);		
		if($input)
		{

			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);
	
			if ($this->form_validation->run() == FALSE) {
					foreach ($val as $key => $value) {
						$data[$key] = form_error($key);
					}
					$this->response($data, 400);
			  }
			  else
			  {
						$val = [
							'name' => $input['name'],
							'code' => $input['code']
						];
						$id = $this->organitation->insert($val);

						$da = array_merge(['id'=>$id],$val);
						$this->response($da,200);
			  }			
		}
	}

	/**
     * @OA\Post(
     *     path="/organitation/update",
     *     tags={"organitation"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/OrganitationModel"
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
     *		ref="#/components/schemas/OrganitationModel"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function update_post()
	{

		$val = [					
			'id' => 'id',
			'name' => 'name',
			'code' => 'code'
		];

		$data = array('success' => false, 'messages' => array());
		$input = json_decode(trim(file_get_contents('php://input')), true);		
		if($input)
		{
			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);
	
			if ($this->form_validation->run() == FALSE) {
					foreach ($val as $key => $value) {
						$data['messages'][$key] = form_error($key);
					}							
					$this->response($data, 400);
				}
				else
				{
						$val = [
							'name' => $input['name'],
							'code' => $input['code']
						];
						$wh = ['id'=> $input['id'] ];
	
						$cc = $this->organitation->update($wh,$val);
	
						if($cc > 0)
						{
						
							$data = array_merge($input);
							$this->response($data, 200);
	
						}
						else
	
						{
							$data = [
								'success' =>false,
								'message'=> "invalid field update"
							];
							$this->response($data, 400);
						}
	
				}			
		}
	}

	/**
     * @OA\Get(
     *     path="/organitation/remove",
     *     tags={"organitation"},	 
	 *     @OA\Parameter(
     *       name="id",
	 *       description="id organitation",
	 * 		 required=true,
     *       in="query",
     *       @OA\Schema(type="integer",default=null)
     *   ),
     *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\JsonContent(     
 	 *         @OA\Property(property="id", type="integer", default=0),
     *     ),
	 * ),
     *    @OA\Response(response="401", description="Unauthorized"),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function remove_get()
	{
		$id = $this->input->get('id');
		$da = $this->organitation->delete(['id'=>$id]);

		if($da > 0)
		{
			$data = ['id'=>$id];
			$this->response($data, 200);
		}
		else
		{
			$data = ['status'=>false, 'message'=> 'delete id '.$id.' failed'];
			$this->response($data, 400);
		}
		
	}

}
