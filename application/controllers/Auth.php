<?php
defined('BASEPATH') or exit('No direct script access allowed');

use OpenApi\Annotations as OA;


class Auth extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('curl');
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
	public function loginWithSFI_post()
	{
		//read data
		$input = json_decode(trim(file_get_contents('php://input')), true);
		$userName = $input["username"];
		$password = $input["password"];

		/* Endpoint */
		$url = 'https://uat.sfi.co.id/sufismart_ci/TestApi/checklogin';


		/* Data */
		$data = [
			'username' => $userName,
			'password' => $password,
		];


		$result =  $this->curl->simple_post($url, $data, array(CURLOPT_BUFFERSIZE => 10));

		if ($result) {
			$user = json_decode($result);

			if ($user->status == "1") {
				$res = (array) $this->auth->createIfNotFound($user->username, $user->fullname, $this->key->lockhash($password), 302);
				if ($res) {
					$token = $this->_createJWToken($userName, $res);
					unset($res['parent_user_id']);
					$data = array_merge($res, ['token' => $token]);
					$this->response($this->responses->successWithData($data, 1), 200);
				} else {
					$this->response("account notfound", 403);
				}
			} else {
				$this->response("account notfound", 403);
			}
		} else {
			$this->response("login failed", 403);
		}
	}
}
