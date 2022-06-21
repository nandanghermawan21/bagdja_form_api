<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @OA\Schema(schema="FormModel")
 */
class Form_model extends CI_Model
{
    /**
     * @OA\Property()
     * @var integer
     */
    public $id;
    /**
     * @OA\Property()
     * @var integer
     */
    public $code;
    /**
     * @OA\Property()
     * @var string
     */
    public $name;

    public function getApplicationForms($applicationId, &$refTotal)
    {
        $sql = "select distinct 
                    f.id, 
                    f.code,
                    f.name
                from sys_application as a
                join sys_application_state as s on a.id = s.application_id
                join sys_form as f on s.form_id = f.id
                where a.id = ?";

        $query = $this->db->query($sql, array($applicationId));
        $refTotal = $query->num_rows();
        
        return $query->result();
    }

    public function getFormPages($formId, &$refTotal)
    {
        $sql = "select * from [sys_form_page]
                WHERE [form_id] = ?
                ORDER by [order] ASC";

        $query = $this->db->query($sql, array($formId));
        $refTotal = $query->num_rows();
        
        return $query->result();
    }
}

/**
 * @OA\Schema(schema="FormPage")
 */
class QuestionGroupInput
{
    /**
     * @OA\Property()
     * @var int
     */
    public $id;
    /**
     * @OA\Property()
     * @var int
     */
    public $form_id;
    /**
     * @OA\Property()
     * @var string
     */
    public $name;
    /**
     * @OA\Property()
     * @var int
     */
    public $order;
}

