<?php
defined('BASEPATH') or exit('No direct script access allowed');

use OpenApi\Annotations as OA;


class Auth extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model', 'auth');
		$this->load->model('Responses_model', 'responses');
	}

	/**
	 * @OA\Post(
	 *     path="/auth/login",
	 *     tags={"auth"},
	 *    @OA\Response(response="200",
	 * 		description="Success",
	 *      @OA\JsonContent(
	 *		ref="#/components/schemas/AuthModel"
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
	 *          @OA\Property(property="username",description="Login username.",type="string"),
	 *          @OA\Property(property="password",description="Login Password.",type="string"),
	 *     ),
	 *   ),
	 * )
	 */
	public function login_post()
	{
		$val = [
			'username' => 'username',
			'password' => 'password'
		];

		$input = json_decode(trim(file_get_contents('php://input')), true);
		if ($input) {
			$this->form_validation->set_data($input);
			foreach ($val as $row => $key) :
				$this->form_validation->set_rules($row, $key, 'trim|required|xss_clean');
			endforeach;
			$this->form_validation->set_error_delimiters(null, null);

			if ($this->form_validation->run() == FALSE) {
				$message = array();
				foreach ($val as $key => $value) {
					$message[$key] = form_error($key);
				}
				$this->response($message, 400);
			} else {

				$user = array(
					'username' => $input['username']
				);

				$cek = $this->auth->login($user);

				if ($cek) {
					if ($this->key->openhash($input['password'], $cek->password)) {
						$res = (array) $cek;
						$token = $this->_createJWToken($input['username'], $res);
						unset($res['parent_user_id']);
						$data = array_merge($res, ['token' => $token]);
						$this->response($this->responses->successWithData($data, 1), 200);
					} else {
						$this->response(['invalid username or password'], 400);
					}
				} else {
					$this->response(['invalid username or password'], 400);
				}
			}
		}
	}

	/**
	 * @OA\Post(
	 *     path="/auth/loginWithSFI",
	 *     tags={"auth"},
	 * 	   description="login for CMO mobile application",
	 *     @OA\Parameter(
	 *       	name="username",
	 *       	description="id",
	 *       	in="query",
	 *       	@OA\Schema(type="integer",default=null)
	 *     ),	 
	 *     @OA\Parameter(
	 *       	name="value",
	 *       	description="value",
	 *       	in="query",
	 *       	@OA\Schema(type="String",default=null)
	 *     ),	 
	 *    security={{"bearerAuth": {}}},
	 *    @OA\Response(response="401", description="Unauthorized"),
	 *    @OA\Response(response="200", 
	 * 		description="Response data inside Responses model",
	 *      @OA\JsonContent(
	 *        ref="#/components/schemas/AuthModel"
	 *      ),
	 *    ),
	 *   )
	 */
	public function loginWithSFI_post()
    {
		//read data
		$userName = $this->input->get('username');
		$password = $this->input->get('password');

        /* Endpoint */
	   $url = 'https://uat.sfi.co.id/sufismart_ci/TestApi/checklogin';
   
	   /* eCurl */
	   $curl = curl_init($url);
  
	   /* Data */
	   $data = [
		   'username'=> $userName, 
		   'password'=> $password,
	   ];
  
	   /* Set JSON data to POST */
	   curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		   
	   /* Define content type */
	   curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		   
	   /* Return json */
	   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		   
	   /* make request */
	   $result = curl_exec($curl);
			
	   /* close curl */
	   curl_close($curl);

	   $this->response($result, 200);
    }
}
