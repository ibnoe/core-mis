<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Clients Model
 * 
 * @package	amartha
 * @author 	fikriwirawan
 * @since	1 December 2013
 */
 
class branch_model extends MY_Model {

    protected $table        = 'tbl_branch';
    protected $key          = 'branch_id';
    protected $soft_deletes = true;
    protected $date_format  = 'datetime';
    
    public function __construct()
	{
        parent::__construct();
    }    
	
	
	
	public function get_all(){
		$this->db->where('deleted', '0')
				 ->order_by('branch_name', 'ASC');
        return $this->db->get($this->table);    
    }
	
	
	public function get_all_sort_by_id(){
		$this->db->where('deleted', '0')
				 ->order_by('branch_id', 'ASC');
        return $this->db->get($this->table);    
    }
	
	
	public function get_branch($branch){
		$this->db->where('deleted', '0')
				 ->where('branch_id', $branch);
        return $this->db->get($this->table);    
    }
	
	public function count_branch($search)
	{
		return $this->db->select("count(branch_id) as numrows")
						->from("tbl_branch")
						->where('deleted','0')
						->get()
						->row()
						->numrows;
	}
}