<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Target Model
 * 
 * @package	amartha
 * @author 	fikriw
 * @since	11 September 2015
 */
 
class target_officer_model extends MY_Model {

    protected $table        = 'tbl_target_officer';
    protected $key          = 'target_officer_id';
    protected $soft_deletes = true;
    protected $date_format  = 'datetime';
    
    public function __construct()
	{
        parent::__construct();
    }  
	
	public function get_all($target_id,$branch=''){
		
		if($branch == 0){$branch = ""; }
		if($target_id){		
			$this->db->join('tbl_target', 'tbl_target.target_id = tbl_target_officer.target_officer_target_id', 'left')
					 ->join('tbl_branch', 'tbl_branch.branch_id = tbl_target.target_branch', 'left')
					 ->join('tbl_officer', 'tbl_officer.officer_id = tbl_target_officer.target_officer_officer', 'left')
					 ->where('tbl_target_officer.target_officer_target_id', $target_id)
					 ->where('tbl_target.deleted', '0')
					 ->where('tbl_target_officer.deleted', '0')
					 ->like('target_branch',$branch)
					 ->order_by('target_officer_id', 'DESC');
			return $this->db->get($this->table)->result();    
		}else{
			$this->db->join('tbl_target', 'tbl_target.target_id = tbl_target_officer.target_officer_target_id', 'left')
					 ->join('tbl_branch', 'tbl_branch.branch_id = tbl_target.target_branch', 'left')
					 ->join('tbl_officer', 'tbl_officer.officer_id = tbl_target_officer.target_officer_officer', 'left')
					 ->where('tbl_target.deleted', '0')
					 ->where('tbl_target_officer.deleted', '0')
					 ->like('target_branch',$branch)
					 ->order_by('target_officer_id', 'DESC');
			return $this->db->get($this->table)->result();    
		}
    }


	public function count_all_target($target_id, $branch='')
	{
		if($target_id){		
			return $this->db->select("count(*) as numrows")
							->join('tbl_target', 'tbl_target.target_id = tbl_target_officer.target_officer_target_id', 'left')
							->join('tbl_branch', 'tbl_branch.branch_id = tbl_target.target_branch', 'left')
							->join('tbl_officer', 'tbl_officer.officer_id = tbl_target_officer.target_officer_officer', 'left')
							->from($this->table)
							->where('tbl_target_officer.target_officer_target_id', $target_id)
							->where('tbl_target.deleted', '0')
							->where('tbl_target_officer.deleted', '0')
							 ->like('target_branch',$branch)
							->get()
							->row()
							->numrows;
		}else{
			return $this->db->select("count(*) as numrows")
							->join('tbl_target', 'tbl_target.target_id = tbl_target_officer.target_officer_target_id', 'left')
							->join('tbl_branch', 'tbl_branch.branch_id = tbl_target.target_branch', 'left')
							->join('tbl_officer', 'tbl_officer.officer_id = tbl_target_officer.target_officer_officer', 'left')
							->from($this->table)
							->where('tbl_target.deleted', '0')
							->where('tbl_target_officer.deleted', '0')
							 ->like('target_branch',$branch)
							->get()
							->row()
							->numrows;
		}
	}
	
	public function get_target_officer_by_id($id){
		$this->db	->join('tbl_target', 'tbl_target.target_id = tbl_target_officer.target_officer_target_id', 'left')
					->join('tbl_branch', 'tbl_branch.branch_id = tbl_target.target_branch', 'left')
					->join('tbl_officer', 'tbl_officer.officer_id = tbl_target_officer.target_officer_officer', 'left')
					->where('target_officer_id', $id)
					->where('tbl_target_officer.deleted', '0');
        return $this->db->get($this->table)->result_array();    
    }
	
	
}