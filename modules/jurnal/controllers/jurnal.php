<?php

class Jurnal extends Front_Controller{
	
	private $per_page 	= '10';
	private $title 		= 'Jurnal';
	private $module 	= 'jurnal';
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model('jurnal_model');
		$this->load->library('pagination');	
	}
	
	public function index($page='0'){
		if($this->session->userdata('logged_in'))
		{
			$total_rows = $this->jurnal_model->count_all($this->input->get('q'));
			
			//pagination
			$config['base_url']     = site_url($this->module.'/all/');
			$config['total_rows']   = $total_rows;
			$config['per_page']     = 15; 
			$config['uri_segment']  = 3;
			$config['suffix'] 		= '?' . http_build_query($_GET, '', "&");
			$config['first_url'] 	= $config['base_url'] . $config['suffix'];
			$config['num_links'] = 2;
			$config['full_tag_open'] = '<li>';
			$config['full_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li><a href="#"><b>';
			$config['cur_tag_close'] = '</b></a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			
			$this->pagination->initialize($config);
			$no =  $this->uri->segment(3);
			
			//$group = $this->jurnal_model->get_group()->result();	
			$jurnal = $this->jurnal_model->get_all( $config['per_page'] ,$page,$this->input->get('q'));			
			
			//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => '',
							'activity_remarks'      => 'Browse Jurnal'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
					
					
			$this->template	->set('menu_title', 'Jurnal Harian')
							->set('menu_jurnal', 'active')
							->set('group_total',$config['total_rows'])
							->set('jurnal', $jurnal)
							->set('no', $no)
							->set('config', $config)
							->build('jurnal');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	public function all($page='0'){
		if($this->session->userdata('logged_in'))
		{
			$total_rows = $this->jurnal_model->count_all($this->input->get('q'));
			
			//pagination
			$config['base_url']     = site_url($this->module.'/all/');
			$config['total_rows']   = $total_rows;
			$config['per_page']     = 15; 
			$config['uri_segment']  = 3;
			$config['suffix'] 		= '?' . http_build_query($_GET, '', "&");
			$config['first_url'] 	= $config['base_url'] . $config['suffix'];
			$config['num_links'] = 2;
			$config['full_tag_open'] = '<li>';
			$config['full_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li><a href="#"><b>';
			$config['cur_tag_close'] = '</b></a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			
			$this->pagination->initialize($config);
			$no =  $this->uri->segment(3);
			
			//$group = $this->jurnal_model->get_group()->result();	
			$jurnal = $this->jurnal_model->get_all( $config['per_page'] ,$page,$this->input->get('q'));			
			
			
			//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => '',
							'activity_remarks'      => 'Browse Jurnal'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
					
					
			$this->template	->set('menu_title', 'Jurnal Harian')
							->set('menu_jurnal', 'active')
							->set('group_total',$config['total_rows'])
							->set('jurnal', $jurnal)
							->set('no', $no)
							->set('config', $config)
							->build('jurnal');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	public function jurnal_add(){
		$id =  $this->uri->segment(3);
		//$data = $this->group_model->find($id);
		
		if($this->save_jurnal()){
			$this->session->set_flashdata('message', 'success|Majelis telah diedit');
			redirect($this->module.'/');
		}
		
		//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => '',
							'activity_remarks'      => 'Add Jurnal'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
					
					
		//GET SPECIFIC PROJECT
		$this->template	->set('data', $data)
						->set('menu_title', 'Tambah Jurnal')
						->set('officer', $officer)
						->set('menu_jurnal', 'active')
						->build('jurnal_form');	
	}
	
	public function delete($id = '0'){
		$this->module = "group";
		$id =  $this->uri->segment(3);
			if($this->group_model->delete($id)){
				$this->session->set_flashdata('message', 'success|Majelis telah dihapus');
				redirect('group/');
				exit;
			}
	}	
	
	private function save_jurnal(){
		
		//set form validation
		$this->form_validation->set_rules('jurnal_desc', 'Deskripsi', 'required');
	
	
		if($this->form_validation->run() === TRUE){
			$id = $this->input->post('jurnal_id');
			
			//process the form
			$data = array(
					'jurnal_tgl'       		=> $this->input->post('jurnal_tgl'),
					'jurnal_code' 			=> $this->input->post('jurnal_code'),			
					'jurnal_desc'       	=> $this->input->post('jurnal_desc'),
					'jurnal_debet'	    	=> $this->input->post('jurnal_debet'),
					'jurnal_credit'	    	=> $this->input->post('jurnal_credit'),
					'jurnal_remark'	   		=> $this->input->post('jurnal_remark'),
			);
				
			if(!$id){
					//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => json_encode($data),
							'activity_remarks'      => 'INSERT Jurnal'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
				return $this->jurnal_model->insert($data);
			}else{
				//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => json_encode($data),
							'activity_remarks'      => 'UPDATE Jurnal'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
				return $this->jurnal_model->update($id, $data);
			} 
		}
	}
	


}