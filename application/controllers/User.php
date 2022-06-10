<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User extends MY_Controller {

	public function __construct()
    {
        parent::__construct();		
        $this->_authenticate();
		$this->load->model('User_model', 'user');
    }



	/**
     * @OA\Get(
     *     path="/user",
     *     tags={"user"},
	 * 	   description="Get all user param id null, get specific with param id",
	 *     @OA\Parameter(
     *       name="id",
	 *       description="user id",
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
	 *				ref="#/components/schemas/UserModel"               
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
			$da = $this->user->view();
		}
		else
		{
			$da = $this->user->view(['id'=>$id]);
		}        

		$this->response($da,200);
	}

	/**
     * @OA\Get(
     *     path="/user/org",
     *     tags={"user"},
	 * 	   description="Get all user param id null, get specific with param id",
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
	 *				ref="#/components/schemas/UserOrg"               
     *             )
     *         ),
     *     ),
     *   ),
	 * )
     */

	public function org_get()
	{		

        $id = $this->input->get('id');
		$da = $this->user->org(['csm_organitation.id'=>$id]);

		$this->response($da,200);
	}


	/**
     * @OA\Post(
     *     path="/user/create",
     *     tags={"user"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/UserModel"
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
     *		ref="#/components/schemas/UserInput"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

	public function create_post()
	{        

		$val = [
			'username' => 'Username',
			'password' => 'Password',
			'nama' => 'Nama',
            'alamat' => 'Alamat',
			'nohp' => 'No HP',
			'email' => 'Email',
			'organitation_id' => 'organitation_id',   
			'parent_user_id' => 'parent_user_id',        
		];
		
		$input = json_decode(trim(file_get_contents('php://input')), true);	
		
			
		if($input)
		{

			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				if($row == 'email')
				{
					$this->form_validation->set_rules($row, $key, 'trim|required|valid_email|xss_clean');
				}
				else
				{
					$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
				}
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
						$field = [
							'username' => $input['username'],
							'password' => $this->key->lockhash(trim($input['password'])),
							'nama' => $input['nama'],
							'alamat' => $input['alamat'],
							'nohp' => $input['nohp'],
							'email' => $input['email'],
							'organitation_id' => $input['organitation_id'],   
							'parent_user_id'=>$input['parent_user_id'],                  
						];
						$id = $this->user->insert($field);
						$da = array_merge(['id'=>$id],$field);
						$this->response($da,200);			
												
			  }					
		}
	}

	/**
     * @OA\Post(
     *     path="/user/update",
     *     tags={"user"},
     *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
     *		ref="#/components/schemas/UserModel"
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
     *		ref="#/components/schemas/UserModel"
     *     ),
     *   ),
	 *   security={{"bearerAuth": {}}},
     * )
     */

    public function update_post()
	{
		$val = [
			"id" => "id",
			'username' => 'Username',
			'password' => 'Password',
			'nama' => 'Nama',
            'alamat' => 'Alamat',
			'nohp' => 'No HP',
			'email' => 'Email',
			'organitation_id' => 'organitation_id',   
			'parent_user_id' => 'parent_user_id',        
		];
				
		$input = json_decode(trim(file_get_contents('php://input')), true);		
		if($input)
		{

			$this->form_validation->set_data($input);
			foreach($val as $row => $key) :
				if($row == 'email')
				{
					$this->form_validation->set_rules($row, $key, 'trim|required|valid_email|xss_clean');
				}
				else
				{
					$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
				}
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
						$field = [
							'username' => $input['username'],
							'password' => $this->key->lockhash(trim($input['password'])),
							'nama' => $input['nama'],
							'alamat' => $input['alamat'],
							'nohp' => $input['nohp'],
							'email' => $input['email'],
							'organitation_id' => $input['organitation_id'],   
							'parent_user_id'=>$input['parent_user_id'],                  						           
						];
	
						$wh = ['id'=> $input['id'] ];
						$cc = $this->user->update($wh,$field);
						if($cc > 0)
						{						
							$da = array_merge(['id'=>$input['id']],$field);														
							$this->response($da,200);	
						}
						else
						{
							$data = [
								'success' =>false,
								'message'=> "invalid field user update"
							];
							$this->response($data,400);	
						}				
			  }		
			echo json_encode($data);
		}
	}
	
	/**
     * @OA\Get(
     *     path="/user/remove",
     *     tags={"user"},	 
	 *     @OA\Parameter(
     *       name="id",
	 * 		 required=true,
	 *       description="user id",
     *       in="query",
     *       @OA\Schema(type="integer",default=null)
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
    public function remove_get()
	{
		$id = $this->input->get('id');
		$da = $this->user->delete(['id'=>$id]);
		
		if($da > 0)
		{
			$data = ['id'=>$id];
			$this->response($data, 200);
		}
		else
		{
			$data = ['status'=>false, 'message'=> 'delete user id '.$id.' failed'];
			$this->response($data, 400);
		}	
	}


	

}
