<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Report Model
 * 
 * @package	amartha
 * @author 	fikriwirawan
 * @since	3 September 2015
 */
 
class presence_model extends MY_Model {

    protected $table        = 'tbl_transaction';
    protected $key          = 'tr_id';
    protected $soft_deletes = true;
    protected $date_format  = 'datetime';
    
    public function __construct()
	{
        parent::__construct();
    }    
	
	
	
	
	//Count Report All
	public function count_presence($kehadiran,$branch, $date_start, $date_end)
	{
		
		if($kehadiran == "h"){ $column = "tr_absen_h";}
		elseif($kehadiran == "s"){ $column = "tr_absen_s";}
		elseif($kehadiran == "c"){ $column = "tr_absen_c";}
		elseif($kehadiran == "i"){ $column = "tr_absen_i";}
		elseif($kehadiran == "a"){ $column = "tr_absen_a";}
		
		return $this->db->select("sum($column) as numrows")
						->join('tbl_group', 'tbl_group.group_id = tbl_transaction.tr_group', 'left')
						->join('tbl_branch', 'tbl_branch.branch_id = tbl_group.group_branch', 'left')
						->from($this->table)
						->where('tbl_transaction.deleted','0')
						->where('tbl_branch.branch_id',$branch)
						->where("tr_date >= '".$date_start."'")
						->where("tr_date <= '".$date_end."'")
						->get()
						->row()
						->numrows;
	}
		
	
	
	
	
	
	
	
}