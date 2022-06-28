<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="AuthModel")
 */

class Auth_model extends CI_Model
{
     /**
      * @OA\Property()
      * @var integer
      */

     public $id;

     /**
      * @OA\Property()
      * @var string
      */

     public $username;


     /**
      * @OA\Property()
      * @var string
      */

     public $nama;


     /**
      * @OA\Property()
      * @var string
      */

     public $alamat;

     /**
      * @OA\Property()
      * @var string
      */

     public $nohp;


     /**
      * @OA\Property()
      * @var string
      */

     public $email;

     /**
      * @OA\Property()
      * @var string
      */

     public $password;


     /**
      * @OA\Property()
      * @var int
      */

     public $organitation_id;




     /**
      * @OA\Property()
      * @var int
      */

     public $parent_user_id;


     /**
      * @OA\Property()
      * @var string
      */

     public $token;



     function login($where)
     {
          $query = $this->db->get_where('usm_users', $where, 1);
          if ($query->num_rows() == 1) {
               return $query->row();
          } else {
               return false;
          }
     }

     function createIfNotFound($username, $fullname, $password, $organitation_id)
     {
          $cek = $this->auth->login(["username" => $username, "organitation_id" => 302]);

          if ($cek == false) {
               $queryInsertUser = "INSERT INTO usm_users (
                  [username],
                  [password],
                  [name],
                  [organitation_id]) VALUES (
                      '" . $username . "',
                      '" . $password . "',
                      '" . $fullname . "',
                      " . $organitation_id . "
                  );";
               $this->db->query($queryInsertUser);
               return $cek = $this->auth->login(["username" => $username, "organitation_id" => 302]);
          } else {
               return $cek;
          }
     }
}
