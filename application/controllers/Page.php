<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_authenticate();

        $this->load->model('Page_model', 'page');
        $this->load->model('Responses_model', 'responses');
    }

    /**
     * @OA\Get(
     *     path="/page/questions",
     *     tags={"page"},
     * 	   description="Get all quustion group on page",
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
     *				ref="#/components/schemas/PageQuestionDetail"               
     *             )
     *         ),
     *     ),
     *   ),
     * )
     */
    public function questions_get()
    {
        $id = $this->get("id");
        $total = 0;
        $data = null;
        $data = $this->page->getQuestions($id, $total);

        $response = $this->responses->successWithData($data, $total);
        $this->response($response, 200);
    }
}
