<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Doc extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
		/**
		 * @OA\Info(title="Mockup Api Survey", version="0.1")				 
		 * @OA\SecurityScheme(
		*      securityScheme="bearerAuth",
		*      in="header",
		*      name="bearerAuth",
		*      type="http",
		*      scheme="bearer",
		*      bearerFormat="JWT",
		* ),
		 */
		$this->load->view('doc');


		/**
		 * @OA\Schema(schema="Unauthorized",
		 *  @OA\Property(property="status", default=false,type="boolean"),
         *   @OA\Property(property="message",type="string"),   
		 * )
		 */

		/**
		 * @OA\Schema(schema="required",
		 *  @OA\Property(property="status", default=false,type="boolean"),
		 *   @OA\Property(property="message", default="field required",type="string"),
		 * )
		 */



	}

	public function swagger()
	{		
		$this->load->view('up');
	}

	public function api()
	{
		$d = $this->mdata->view_all('csm_organitation');		

		if(count($d) > 0)
		{		
			foreach($d as $row)
			{
				$ab []=  $row->id;
			}
		}
		else
		{
			$ab = [0,1,2,3];
		}

		$da = [
			"openapi"=> '3.0.0',
			"info"=> [
				'title'=> 'Mockup Api Survey',
				'version'=> 1,
			],
			'servers'=> [
				['url'=> base_url() ]
			],
			'paths'=> [
				//auth
				'/auth/login'=>[
					'post'=>[
						'tags'=>['auth'],
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'$ref' => '#/components/schemas/auth'
									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Success login',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/ValidLogin'
										],
									]
								]
							],
							'400'=> [
								'description'=>'Bad Request, invalid data user',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/InvalidLogin'
										],
									]
								]
							],
						]


					]

				],
				//organitation
				'/organitation'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['organitation'],
						'description'=>'Get all organitation param id null, get specific with param id',
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id organitation',
								'in'=>'query',
								'required'=> false,
								'schema' => [
									'type' => 'integer',	
									'default'=> null							
									]			
							]
							
						],						
						'responses'=>[
							'200'=> [
								'description'=>'Get Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/org'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],
						],
					]

				],
				'/organitation/create'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['organitation'],
						'operationId'=>'Create organitation',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'name' => [
												'type'=>'string'											
											],
											'code' => [
												'type'=>'string'												
											]
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Create Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'create organitation success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/organitation/update'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['organitation'],
						'operationId'=>'Update organitation',						
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'id' => [
												'type'=>'integer',												
											],
											'name' => [
												'type'=>'string',												
											],
											'code' => [
												'type'=>'string',												
											]
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Update Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'update organitation success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/organitation/remove'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['organitation'],
						'operationId'=>'Delete organitation',						
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id Organitation',
								'in'=>'query',
								'required'=> true,
								'schema' => [
									'type' => 'integer',										
									]			
							]
							
						],	
						'responses'=>[
							'200'=> [
								'description'=>'Delete Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'delete organitation success'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				//level
				'/level'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['level'],
						'description'=>'Get all Level param id null, get specific with param id',
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id Level',
								'in'=>'query',
								'required'=> false,
								'schema' => [
									'type' => 'integer',	
									'default'=> null							
									]			
							]
							
						],						
						'responses'=>[
							'200'=> [
								'description'=>'Get Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/level'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/level/create'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['level'],
						'operationId'=>'Create Level',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'id' => [
												'type'=>'integer',
												'enum' => $ab,	
												'required'=>true,
											],
											'child_id' => [
												'type'=>'integer',
												'required'=>true,
											]
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Create Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'create level success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/level/update'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['level'],
						'operationId'=>'Update Level',						
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'id' => [
												'type'=>'integer',
												'required'=>1,
											],
											'name' => [
												'type'=>'string',
												'required'=>1,
											],
											'code' => [
												'type'=>'string',
												'required'=>1,
											]
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Update Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'update level success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/level/remove'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['level'],
						'operationId'=>'Delete Level',						
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id Level',
								'in'=>'query',
								'required'=> true,
								'schema' => [
									'type' => 'integer',										
									]			
							]
							
						],	
						'responses'=>[
							'200'=> [
								'description'=>'Delete Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'delete level success'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				//user
				'/user/create'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['user'],
						'operationId'=>'Create User',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'username' => [
												'type'=>'string',
												'required'=>1,
											],
											'password' => [
												'type'=>'string',
												'required'=>1,
											],
											'nama' => [
												'type'=>'string',
												'required'=>1,
											],
											'alamat' => [
												'type'=>'string',
												'required'=>1,
											],
											'nohp' => [
												'type'=>'string',
												'required'=>1,
											],
											'email' => [
												'type'=>'string',
												'required'=>1,
											],
											'organitation_id' => [
												'type'=>'string',
												'enum' => $ab,											
												'required'=>1,
											],				
											'parent_user_id' => [
												'type'=>'integer',
												'required'=>1,
											]

										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Create Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'create user success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/user'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['user'],
						'description'=>'Get All User param id null, get specific with param id',	
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id user',
								'in'=>'query',
								'required'=> false,
								'schema' => [
									'type' => 'integer',	
									'default'=> null							
									]			
							]
							
						],		
						'responses'=>[
							'200'=> [
								'description'=>'Create Success'
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],				
				'/user/update'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['user'],
						'operationId'=>'Update User',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'id' => [
												'type'=>'integer',
												'required'=>1,
											],
											'username' => [
												'type'=>'string',
												'required'=>1,
											],
											'password' => [
												'type'=>'string',
												'required'=>1,
											],
											'nama' => [
												'type'=>'string',
												'required'=>1,
											],
											'alamat' => [
												'type'=>'string',
												'required'=>1,
											],
											'nohp' => [
												'type'=>'string',
												'required'=>1,
											],
											'email' => [
												'type'=>'string',
												'required'=>1,
											],
											'organitation_id' => [
												'type'=>'integer',
												'required'=>1,
											],
											'parent_user_id' => [
												'type'=>'integer',
												'required'=>1,
											]

										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Update Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'update user success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/user/delete'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['user'],
						'operationId'=>'Delete User',	
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id User',
								'in'=>'query',
								'required'=> true,
								'schema' => [
									'type' => 'integer',										
									]			
							]
							
						],	
						'responses'=>[
							'200'=> [
								'description'=>'Delete Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'delete user success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				// collection list
				'/collection/list'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection list'],
						'description'=>'Get all collection param id null, get specific with param id',
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id collection',
								'in'=>'query',
								'required'=> false,
								'schema' => [
									'type' => 'integer',	
									'default'=> null							
									]			
							]
							
						],						
						'responses'=>[
							'200'=> [
								'description'=>'Get Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/collection'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/list/create'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection list'],
						'operationId'=>'Create collection',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[							
											'name' => [
												'type'=>'string',
												'required'=>true,
											]
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Create Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'create collection success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/list/update'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection list'],
						'operationId'=>'Update collection',						
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[
											'id' => [
												'type'=>'integer',
												'required'=>1,
											],
											'name' => [
												'type'=>'string',
												'required'=>1,
											]						
										],
										'type'=>'object'

									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Update Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'update collection success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/list/remove'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection list'],
						'description'=>'Delete collection',						
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id collection',
								'in'=>'query',
								'required'=> true,
								'schema' => [
									'type' => 'integer',										
									]			
							]
							
						],							
						'responses'=>[
							'200'=> [
								'description'=>'Delete Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'delete collection success'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'400'=> [
								'description'=>'Bad request',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'delete collection invalid data'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],

				// collection data
				'/collection/data'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection data'],
						'description'=>'Get all collection param id null, get specific with param id',
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id collection',
								'in'=>'query',
								'required'=> false,
								'schema' => [
									'type' => 'integer',	
									'default'=> null							
									]			
							]
							
						],						
						'responses'=>[
							'200'=> [
								'description'=>'Get Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/collection'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/data/create'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection data'],
						'operationId'=>'Create collection',
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[							
											'collection_id' => [
												'type'=>'integer',
												'required'=>true,
											],
											'label' => [
												'type'=>'string',
												'required'=>true,
											],
											'value' => [
												'type'=>'string',
												'required'=>true,
											]
										],
										'type'=>'object'
									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Create Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'create collection success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'field required'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/data/update'=>[
					'post'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection data'],
						'operationId'=>'Update collection data',						
						'requestBody'=> [
							'content'=>[
								'application/json'=>[
									'schema'=> [
										'properties'=>[							
											'collection_id' => [
												'type'=>'integer',
												'required'=>true,
											],
											'label' => [
												'type'=>'string',
												'required'=>true,
											],
											'value' => [
												'type'=>'string',
												'required'=>true,
											]
										],
										'type'=>'object'
									],
								]
							]
						],
						'responses'=>[
							'200'=> [
								'description'=>'Update Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'update collection data success'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'400'=> [
								'description'=>'field required',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'update collection data failed'
					
											],
											'type'=>'object'
										],
									]
								]
							],
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],
				'/collection/data/remove'=>[
					'get'=>[
						'security'=> [
							["bearerAuth"=>[]]
						],
						'tags'=>['collection data'],
						'description'=>'Delete collection',						
						'parameters'=> [
							[
								'name' => 'id',							
								'description'=> 'id collection',
								'in'=>'query',
								'required'=> true,
								'schema' => [
									'type' => 'integer',										
									]			
							]
							
						],							
						'responses'=>[
							'200'=> [
								'description'=>'Delete Success',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => true,
												'message'=>'delete collection data success'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'400'=> [
								'description'=>'Bad request',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'properties'=>[
												'success' => [
													'type'=>'string',															
												],
												'message' => [
													'type' => 'string',																		
												]
											],
											'example'=> [
												'success' => false,
												'message'=>'delete collection invalid data'
					
											],
											'type'=>'object'
										],
									]
								]
							],	
							'401'=> [
								'description'=>'Unauthorized',
								'content'=>[
									'application/json'=>[
										'schema'=> [
											'$ref' => '#/components/schemas/Unauthorized'
										],
									]
								]
							],

						],
					]

				],


			],
			'components'=>[
				'securitySchemes'=>[
						'bearerAuth'=> [
							'type'=>'http',
							'scheme'=>'Bearer',
							'bearerFormat'=>'JWT',
						]
				],
				'schemas' => [
					'Unauthorized'=> [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'token' => [
								'type' => 'string',																		
							]
						],
						'example'=> [
							'success' => false,
							'token'=> 'Unauthorized'
						],
						'type'=>'object'
					],
					'auth' => [
						'properties'=>[
							'username' => [
								'type'=>'string',												
							],
							'password' => [
								'type'=>'string',												
							]
						],
						'type'=>'object'
					],
					'ValidLogin' => [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'token' => [
								'type' => 'string',																		
							]
						],
						'example'=> [
							'success' => true,
							'token'=> 'eyJ0eXAiOiJKV1QiLCJhbxxxxxxxxx'
						],
						'type'=>'object'
					],
					'InvalidLogin' => [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'token' => [
								'type' => 'string',																		
							]
						],
						'example'=> [
							'success' => false,
							'token'=> 'invalid username or password'
						],
						'type'=>'object'
					],
					'org' => [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'data' => [
								'type' => 'array',																		
							]
						],
						'example'=> [
							'success' => true,
							'data'=> [
								'id' => 1,
								'name'=>'sample',
								"code"=> "HO"
							]

						],
						'type'=>'object'
					],
					'collection' => [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'data' => [
								'type' => 'array',																		
							]
						],
						'example'=> [
							'success' => true,
							'data'=> [
								'id' => 1,
								'name'=>'sample'						
							]

						],
						'type'=>'object'
					],
					'level' => [
						'properties'=>[
							'success' => [
								'type'=>'string',															
							],
							'data' => [
								'type' => 'array',																		
							]
						],
						'example'=> [
							'success' => true,
							'data'=> [
								'parent_id' => 1,
								'child_id'=> 1,								
							]

						],
						'type'=>'object'
					]
				]

			]
		];

		echo json_encode($da);
	}

}
