<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Form extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Form_model', 'form');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/questiongroup/application",
     *     tags={"Form"},
     * 	   description="Get all form on application",
     *     @OA\Parameter(
     *       name="id",
     *       description="id application",
     *       in="query",
     *       @OA\Schema(type="integer",default=1)
     *   ),
     * security={{"bearerAuth": {}}},
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="200", 
     * 		description="Response data inside Responses model",
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(type="array",
     *             @OA\Items(type="object",
     *				ref="#/components/schemas/FormModel"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function application_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        $data = $this->form->get($id, $total);

        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }
}
