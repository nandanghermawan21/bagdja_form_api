<?php
defined('BASEPATH') or exit('No direct script access allowed');

use OpenApi\Annotations as OA;


class Auth extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Methods: GET, DELETE, HEAD, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		$this->load->model('Auth_model', 'auth');
		$this->load->model('Responses_model', 'responses');
	}

	/**
	 * @OA\Post(
	 *     path="/auth/login",
	 *     tags={"Auth"},
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
						$c = $this->_createJWToken($input['username']);

						$res = (array) $cek;
						unset($res['parent_user_id']);
						$token = ['token' => $c];
						$data = array_merge($res, $token);
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
}
