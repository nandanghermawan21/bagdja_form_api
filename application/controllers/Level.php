<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Level extends MY_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->_authenticate();		

		$this->load->model('Level_model', 'level');
    }


		/**
     * @OA\Get(
     *     path="/level",
     *     tags={"level"},
	 * 	   description="Get all Level param id null, get specific with param id",
	 *     @OA\Parameter(
     *       name="parent_id",
	 *       description="Parent id",
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
	 *				ref="#/components/schemas/CollectionModelList"               
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
			$da = $this->level->view(null);
		}
		else
		{
			$da = $this->level->view(['id'=>$id]);
		}

		$this->response($da,200);
	}

	/**
     * @OA\Post(
     *     path="/level/create",
     *     tags={"level"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/LevelModel"
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
     *		ref="#/components/schemas/LevelModel"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function create_post()
	{
		$val = [
			'parent_id' => 'parent id',
			'child_id' => 'child id'
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
					$this->response($data,400);
			  }
			  else
			  {					
						$cc = $this->level->insert($input);
						if($cc > 0)
						{							
							$this->response($input,200);
						}
						else
						{
	
							$data = [
								'success' =>false,
								'message'=>'create level failed'
							];

							$this->response($data,400);

						}
			  }			
		}
	}

	/**
     * @OA\Post(
     *     path="/level/update",
     *     tags={"level"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/LevelModel"
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
     *		ref="#/components/schemas/LevelModel"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function update_post()
	{

				$val = [
					'parent_id' => 'parent id',
					'child_id' => 'child id'
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
							$this->response($data,400);
						}
						else
						{
							$val = [								
								'child_id' => $input['child_id']
							];
								$wh = ['parent_id'=> $input['parent_id'] ];
	
								$cc = $this->level->update($wh,$val);
	
								if($cc > 0)
								{	
								   $this->response($input,200);
	
							   }
							   else
	
							   {
									$data = [
										'success' =>false,
										'message'=> "invalid field update lvel"
									];
									$this->response($data,400);
							   }
	
						}
					echo json_encode($data);

				}
	}

	/**
     * @OA\Get(
     *     path="/level/remove",
     *     tags={"level"},	 
	 *     @OA\Parameter(
     *       name="parent_id",
	 *       description="id level",
     *       in="query",
	 * 		 required=true,
     *       @OA\Schema(type="integer",default=null)
     *   ),
	 * security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
	 * 		description="Success",
	 *      @OA\JsonContent(     
 	 *         @OA\Property(property="parent_id", type="integer", default=0),
     *     ),
     *   ),
	 * )
     */

	public function remove_get()
	{
		$id = $this->input->get('parent_id');
		$da = $this->level->delete(['parent_id'=>$id]);

		if($da > 0)
		{
			$data = ['id'=>$id];
			$this->response($data, 200);
		}
		else
		{
			$data = ['status'=>false, 'message'=> 'delete parent id '.$id.' failed'];
			$this->response($data, 400);
		}
	}



}
