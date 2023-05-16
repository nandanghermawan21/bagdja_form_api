<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom500 extends MY_Controller {

  public function __construct() {

    parent::__construct();

    // load base_url
    $this->load->helper('url');
  }

  public function index(){
    $this->response(500, " Ops.. an error occurred on the server, please try again or contact the administrator");
  }

}