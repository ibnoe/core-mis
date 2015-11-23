<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Clients Model
 * 
 * @package	amartha
 * @author 	fikriwirawan
 * @since	1 December 2013
 */
 
class clients_model extends MY_Model {

    protected $table        = 'tbl_clients';
    protected $key          = 'client_id';
    protected $soft_deletes = true;
    protected $date_format  = 'datetime';
    
    public function __construct()
	{
        parent::__construct();
    }    
	
	public function get_clients()
	{
		$this->db->join('tbl_group', 'tbl_group.group_id = tbl_clients.client_group', 'left');		
		$this->db->join('tbl_branch', 'tbl_branch.branch_id = tbl_group.group_branch', 'left');	
		return $this->db->get($this->table);
	}
	
	public function get_client($id)
	{
		$this->db->join('tbl_group', 'tbl_group.group_id = tbl_clients.client_group', 'left');		
		$this->db->join('tbl_branch', 'tbl_branch.branch_id = tbl_group.group_branch', 'left');		
		$this->db->join('tbl_area', 'tbl_area.area_id = tbl_group.group_area', 'left');	
		$this->db->join('tbl_officer', 'tbl_officer.officer_id = tbl_clients.client_officer', 'left');
		$this->db->where('client_id', $id);		
		return $this->db->get($this->table);
	}
	
	public function get_client_by_group($id)
	{
		return $this->db->where('client_group', $id)
						->where('deleted', '0')
						->get('tbl_clients')
						->result();
	}
	
	
	public function get_pembiayaan_by_group($id)
	{
		return $this->db
						->join('tbl_pembiayaan', 'tbl_pembiayaan.data_id = tbl_clients.client_pembiayaan_id', 'left')
						//->join('tbl_pembiayaan', 'tbl_pembiayaan.data_client = tbl_clients.client_id', 'left')
						->join('tbl_tabwajib', 'tbl_tabwajib.tabwajib_account = tbl_clients.client_account', 'left')
						->join('tbl_tabsukarela', 'tbl_tabsukarela.tabsukarela_account = tbl_clients.client_account', 'left')
						->join('tbl_tabberjangka', 'tbl_tabberjangka.tabberjangka_account = tbl_clients.client_account', 'left')
						//->join('tbl_tabwajib', 'tbl_tabwajib.tabwajib_client = tbl_clients.client_account', 'left')
						//->join('tbl_tabsukarela', 'tbl_tabsukarela.tabsukarela_account = tbl_clients.client_account', 'left')
						//->join('tbl_tabberjangka', 'tbl_tabberjangka.tabberjangka_account = tbl_clients.client_account', 'left')
						->where('client_group', $id)
						->where('tbl_clients.client_status', '1')
						->where('tbl_clients.deleted', '0')
						//->where('tbl_pembiayaan.deleted != 1')
						//->where('tbl_tabwajib.deleted','0')
						//->where('tbl_tabsukarela.deleted','0')
						->order_by('client_subgroup','ASC')
						->order_by('client_account','ASC')
						//->where('tbl_pembiayaan.deleted', '0')
						//->where('tbl_pembiayaan.data_status', '1')
						->get('tbl_clients')
						->result();
	}
	
	public function get_pembiayaan_by_group_by_subgroup($id,$subgroup)
	{
		return $this->db
						->join('tbl_pembiayaan', 'tbl_pembiayaan.data_id = tbl_clients.client_pembiayaan_id', 'left')
						//->join('tbl_pembiayaan', 'tbl_pembiayaan.data_client = tbl_clients.client_id', 'left')
						->join('tbl_tabwajib', 'tbl_tabwajib.tabwajib_account = tbl_clients.client_account', 'left')
						->join('tbl_tabsukarela', 'tbl_tabsukarela.tabsukarela_account = tbl_clients.client_account', 'left')
						->join('tbl_tabberjangka', 'tbl_tabberjangka.tabberjangka_account = tbl_clients.client_account', 'left')
						//->join('tbl_tabwajib', 'tbl_tabwajib.tabwajib_client = tbl_clients.client_account', 'left')
						//->join('tbl_tabsukarela', 'tbl_tabsukarela.tabsukarela_account = tbl_clients.client_account', 'left')
						//->join('tbl_tabberjangka', 'tbl_tabberjangka.tabberjangka_account = tbl_clients.client_account', 'left')
						->where('client_group', $id)
						->where('tbl_clients.client_status', '1')
						->where('tbl_clients.deleted', '0')
						->like('client_subgroup', $subgroup)
						//->where('tbl_pembiayaan.deleted != 1')
						//->where('tbl_tabwajib.deleted','0')
						//->where('tbl_tabsukarela.deleted','0')
						->order_by('client_subgroup','ASC')
						->order_by('client_account','ASC')
						//->where('tbl_pembiayaan.deleted', '0')
						//->where('tbl_pembiayaan.data_status', '1')
						->get('tbl_clients')
						->result();
	}
	
	public function count_client_by_group($id)
	{
		return $this->db->select("count(client_id) as numrows")
						->from('tbl_clients')
						->join('tbl_pembiayaan', 'tbl_pembiayaan.data_id = tbl_clients.client_pembiayaan_id', 'left')
						//->join('tbl_pembiayaan', 'tbl_pembiayaan.data_client = tbl_clients.client_id', 'left')
						->join('tbl_tabwajib', 'tbl_tabwajib.tabwajib_account = tbl_clients.client_account', 'left')
						->join('tbl_tabsukarela', 'tbl_tabsukarela.tabsukarela_account = tbl_clients.client_account', 'left')
						->join('tbl_tabberjangka', 'tbl_tabberjangka.tabberjangka_account = tbl_clients.client_account', 'left')
						->where('client_group', $id)
						//->where('tbl_pembiayaan.deleted', '0')
						->where('tbl_clients.client_status', '1')
						->where('tbl_clients.deleted','0')
						//->where('tbl_pembiayaan.deleted','0')
						->get()
						->row()
						->numrows;
	}
	
	
	
	public function count_absen_s($pembiayaan)
	{
		return $this->db->select("sum(tr_absen_s) as numrows")
						->from('tbl_transaction')
						->where('tbl_transaction.tr_pembiayaan',$pembiayaan)
						->where('tbl_transaction.deleted','0')
						->get()
						->row()
						->numrows;
	}
	public function count_absen_c($pembiayaan)
	{
		return $this->db->select("sum(tr_absen_c) as numrows")
						->from('tbl_transaction')
						->where('tbl_transaction.tr_pembiayaan',$pembiayaan)
						->where('tbl_transaction.deleted','0')
						->get()
						->row()
						->numrows;
	}
	public function count_absen_i($pembiayaan)
	{
		return $this->db->select("sum(tr_absen_i) as numrows")
						->from('tbl_transaction')
						->where('tbl_transaction.tr_pembiayaan',$pembiayaan)
						->where('tbl_transaction.deleted','0')
						->get()
						->row()
						->numrows;
	}
	public function count_absen_a($pembiayaan)
	{
		return $this->db->select("sum(tr_absen_a) as numrows")
						->from('tbl_transaction')
						->where('tbl_transaction.tr_pembiayaan',$pembiayaan)
						->where('tbl_transaction.deleted','0')
						->get()
						->row()
						->numrows;
	}
}