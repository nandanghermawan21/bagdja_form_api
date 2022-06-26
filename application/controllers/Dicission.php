<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dicission extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();
                            
        $this->load->model('Dicissiontype_model', 'dicissiontype');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/dicission/types",
     *     tags={"dicission Types"},
     * 	   description="Get all quustion group param id null, get specific with param id",
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
     *				ref="#/components/schemas/DicissionType"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function types_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        if ($id == null) {
            $data = $this->dicissiontype->get(null, $total);
        } else {
            $data = $this->dicissiontype->get(["id" => $id], $total);
        }
        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }
}