<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Tabungan Berjangka Model
 * 
 * @package	amartha
 * @author 	fikriwirawan
 * @since	1 January 2013
 */
 
class tabsipeci_tr_model extends MY_Model {

    protected $table        = 'tbl_tr_tabsipeci';
    protected $key          = 'tr_id';
    protected $soft_deletes = true;
    protected $date_format  = 'datetime';
    
    public function __construct()
	{
        parent::__construct();
    }    
	
		
	
	
}