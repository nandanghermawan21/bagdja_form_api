<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdata extends CI_Model {

	  // chech or ambil all data
  function check_all($table,$where,$limit)
  {
  	$query = $this->db->get_where($table,$where,$limit);
    if($query->num_rows()==1)
    {
      return $query->row();
    }
    else {
      return false;
    }
  }

	//insert data
	public function insert_all($table,$data)
	{
		$this->db->insert($table,$data);
		return $this->db->affected_rows(); 	
	}

	public function insert_batch($table,$data)
	{
		$this->db->insert_batch($table, $data);
	}

	//update data
	public function update_all($where,$data,$table)
	{
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows(); 	
	}

	//tampil data
	public function view_all($table)
	{
		$query = $this->db->get($table);
		return $query->result();
	}

	//delete data
	public function delete_all($table,$data)
    {
		$this->db->where($data);
		$this->db->delete($table);   
		return $this->db->affected_rows(); 	
    }



  public function likedata($table,$data)
  {
  		$this->db->select("*");
		$this->db->from($table);
		$this->db->like($data);
		$query = $this->db->get();
		return $query->result();
  }


    public function likewhere($table,$like,$where)
  {
  		$this->db->select("*");
		$this->db->from($table);
		$this->db->like($like);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
  }

  public function pagedata($table,$where,$limit,$start,$sama)
  {

	    $this->db->select('*');
		$this->db->from($table[0]);
		$this->db->where($where);
		$this->db->limit($limit,$start);
		$gb = count($sama);
		for ($i=1; $i < $gb; $i++)
		{
	   		$this->db->join($table[$i],$sama[$i],'left');
	    }
		$query = $this->db->get();
		return $query->result();

  }


    public function view_where($table,$where)
    {
    	$query = $this->db->get_where($table,$where);
    	return $query->result();
    }

     public function view_limit($table,$limit)
    {
    	$this->db->select("*");
		$this->db->from($table);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result();
    }


    public function where_view($table,$where,$limit,$offset,$order)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where($where);
		$this->db->limit($limit,$offset);
		$this->db->order_by($order, 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	public function ambildata($table)
	{
		$this->db->select("*");
		$this->db->from($table);
		$query = $this->db->get();
		return $query->result();

	}

	public function ambil($table,$data)
	{
		$this->db->select("*");
		$this->db->from($table);
		$query = $this->db->get();
		//return $query->result();
		return $query->row($data);
	}

	public function join_all($table,$sama,$where)
	{
		$this->db->select('*');
    	$this->db->from($table[0]);
    	if($where!=0)
    	{
    	 $this->db->where($where);
    	}
    	$gb = count($sama);
    	for ($i=1; $i < $gb; $i++)
    	{

       		$this->db->join($table[$i],$sama[$i]);
        }
		$query = $this->db->get();
		return $query->result();
	}



	public function join_group($table,$sama,$where)
	{
		$this->db->select('*');
    	$this->db->from($table[0]);
    	if($where!=0)
    	{
    	 $this->db->where($where);
    	}
    	$gb = count($sama);
    	for ($i=1; $i < $gb; $i++)
    	{

       		$this->db->join($table[$i],$sama[$i]);
        }
		$query = $this->db->get();
		return $query->result();
	}



	public function join_limit($table,$sama,$limit,$where)
	{
		$this->db->select('*');
    	$this->db->from($table[0]);
    	$this->db->where($where);
    	$this->db->limit($limit);
    	$gb = count($sama);
    	for ($i=1; $i < $gb; $i++)
    	{
       		$this->db->join($table[$i],$sama[$i],'left');
        }
		$query = $this->db->get();
		return $query->result();
	}

	public function numdata($table)
	{
		$this->db->select_max('id');
		$query = $this->db->get($table)->row(); 
	}

	public function numdata2($table)
	{
		$this->db->select("*");
		$this->db->from($table);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function sumdata($table,$data,$sum)
	{
		$this->db->select_sum($sum);
		$this->db->from($table);
		$this->db->where($data);
		return $query = $this->db->get();
	}


}
