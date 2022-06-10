<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Collection extends MY_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->_authenticate();		

		$this->load->model('Collection_model', 'collection');
    }

	public function list_get()
	{
		$uri = $this->uri->segment(3);
        
        switch ($uri) {                        
            case null:
            case false:
            case '':
                $this->_show();
                break;           
            case 'delete':
                $this->_remove();
                break;
            default:
                show_404();
                break;
        }
	}

	public function list_post()
	{
		$uri = $this->uri->segment(3);
        
        switch ($uri) {                               
            case 'create':
                $this->_create();
                break;
            case 'update':
                $this->_update();
                break;
            default:
                show_404();
                break;
        }
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
    public function _show()
    {
        $id = $this->input->get('id');
		if($id == null)
		{
			$da = $this->collection->view(null,'sys_collection');
		}
		else
		{
			$da = $this->collection->view(['id'=>$id],'sys_collection');
		}
		$this->response($da, 200);
    }

	/**
     * @OA\Post(
     *     path="/collection/list/create",
     *     tags={"collection"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/CollectionModelList"
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
     *		ref="#/components/schemas/collectionInputList"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

    public function _create()
	{
		$val = [			
			'name' => 'name'
		];
		
		$input = json_decode(trim(file_get_contents('php://input')), true);		
		if($input)
		{
			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);
	
			if ($this->form_validation->run() == FALSE) 
            {
					foreach ($val as $key => $value) {
						$data[$key] = form_error($key);
					}
					$this->response($data,400);
			  }
			  else
			  {
						$id = $this->collection->insert($input,'sys_collection');
						$data = array_merge(['id'=>$id],$input);
						$this->response($data,200);
			  }			
		}
	}


	/**
     * @OA\Post(
     *     path="/collection/list/update",
     *     tags={"collection"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/CollectionModelList"
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
     *		ref="#/components/schemas/CollectionModelList"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function _update()
	{

				$val = [					
					'id' => 'id',
					'name' => 'nanme'					
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
								'name' => $input['name']
							];
								$wh = ['id'=> $input['id'] ];
	
								$cc = $this->collection->update($wh,$val,'sys_collection');
	
								if($cc > 0)
								{
						
								   $this->response($input,200);
	
							   }
							   else
							   {
									$data = [
										'success' =>false,
										'message'=> "invalid field update"
									];
									$this->response($data,400);
							   }
	
						}					

				}
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
		$da = $this->collection->delete(['id'=>$id],'sys_collection');

        if($da > 0)
        {
			$data = ['id'=>$id];
			$this->response($data,200);
        }
        else
        {
            $data = [
                'success' =>false,                
                'message'=>'delete collection invalid data',
            ];
            $this->response($data,400);
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
	 *				ref="#/components/schemas/CollectionModelList"               
     *             )
     *         ),
     *     ),
     *   ),
	 * )
     */

    public function show()
    {
        $id = $this->input->get('id');
		if($id == null)
		{
			$da = $this->collection->view(null,'sys_collection_data');
		}
		else
		{
			$da = $this->mdata->view(['id'=>$id],'sys_collection_data');
		}
		$this->response($da,200);
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
            'collection_id'=> 'collection_id',
            'value'=> 'value',
			'label' => 'label'
		];
		
		$input = json_decode(trim(file_get_contents('php://input')), true);		
		if($input)
		{
			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);
	
			if ($this->form_validation->run() == FALSE) 
            {
					foreach ($val as $key => $value) {
						$data['messages'][$key] = form_error($key);
					}
					$this->response($data,400);
			}
			else
			{
						$this->collection->insert($input,'sys_collection_data');
						$this->response($input,200);
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
                    'collection_id'=> 'collection id',
                    'value'=> 'value',
                    'label' => 'label'
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
							http_response_code('400');
						}
						else
						{
				
								$wh = ['collection_id'=> $input['collection_id'] ];
									
                                $cc = $this->mdata->update_all($wh,$input,'sys_collection_data');
	
								if($cc > 0)
								{
								   $data = [
									   'success' =>true,
									   'message'=>'update collection success'
								   ];
								   http_response_code('200');
	
							   }
							   else
							   {
									$data = [
										'success' =>false,
										'message'=> "update collection failed"
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
		$da = $this->collection->delete(['collection_id'=>$id],'sys_collection_data');

        if($da > 0)
        {
			$data = ['collection_id'=>$id];
			$this->response($data,200);
        }
        else
        {
            $data = [
                'success' =>false,                
                'message'=>'delete collection invalid data',
            ];
            $this->response($data,400);
        }
	}



}
