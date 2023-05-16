<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Tree extends MY_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->_authenticate();		
    }


	public function index()
	{
        $id = $this->input->get('id');
        $wh = ['usm_organitation_level.parent_id'=>$id];

        $tab  = array ('usm_organitation_level','csm_organitation','usm_users');
        $sama   = array (null,'usm_organitation_level.parent_id=csm_organitation.id',
                              'csm_organitation.id=usm_users.organitation_id'
                            );            
		$id = $this->input->get('id');
		if($id == null)
		{
			$da = $this->mdata->join_all($tab,$sama,0);
		}
		else
		{
			$da = $this->mdata->join_all($tab,$sama,$wh);
		}
		$this->response($da, 200);
	}

	public function create()
	{
		$val = [
			'id' => 'id',
			'child_id' => 'child'
		];

		$data = array('success' => false, 'messages' => array());

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
					$val = [
						'parent_id' => $this->input->post('id'),
						'child_id' => $this->input->post('child_id')
					];
					$this->mdata->insert_all('usm_organitation_level',$val);
					$data = [
						'success' =>true,
						'message'=>'create level success'
					];
					http_response_code('200');
		  }
		echo json_encode($data);
	}

	public function update()
	{

				$val = [					
					'id' => 'id',
					'chid_id' => 'child'					
				];

				$data = array('success' => false, 'messages' => array());
					
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
						$val = [
                            'parent_id' => $this->input->post('id'),
                            'child_id' => $this->input->post('child_id')
                        ];
							$wh = ['parent_id'=> $this->input->post('id') ];

							$cc = $this->mdata->check_all('usm_user',$wh,1);

							if($cc)
							{
							   $cc = $this->mdata->update_all($wh,$val,'usm_organitation_level');
							   $data = [
								   'success' =>true,
								   'message'=>'update organitation success'
							   ];
							   http_response_code('200');

						   }
						   else

						   {
								$data = [
									'success' =>false,
									'message'=> "invalid field id"
								];
								http_response_code('400');
						   }

					}
				echo json_encode($data);
	}

	public function remove()
	{
		$id = $this->input->get('id');
		$da = $this->mdata->delete_all('usm_organitation_level',['parent_id'=>$id]);
		
        $data = [
            'success' =>true,
            'data'=>$da,
        ];
        http_response_code('200');

		echo json_encode($data);
	}



}
