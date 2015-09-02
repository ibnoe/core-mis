<?php

class Creditscoring extends Front_Controller{
	
	private $per_page 	= '10';
	private $title 		= 'Creditscoring';
	private $module 	= 'creditscoring';
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model('clients_model');
		$this->load->model('clients_pembiayaan_model');
		
		$this->load->library('pagination');	
	}
	
	
	//CLIENTS
	public function browse($page='0')
	{
		if($this->session->userdata('logged_in'))
		{
			$user_branch = $this->session->userdata('user_branch');	
			//Get Total Pengajuan 
			if($user_branch != 0){
				$total_rows = $this->clients_pembiayaan_model->count_all_pengajuan_by_branch($this->input->get('q'),$user_branch);
				
			}else{
				$total_rows = $this->clients_pembiayaan_model->count_all_pengajuan($this->input->get('q'));
				
			}			
			
				
			//pagination
			$config['base_url']     = site_url($this->module.'/browse');
			$config['total_rows']   = $total_rows;
			$config['per_page']     = 15; 
			$config['uri_segment']  = 3;
			//$config['suffix'] 		= '?' . http_build_query($_GET, '', "&");
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
			if($user_branch != 0){
				$clients = $this->clients_pembiayaan_model->get_all_pengajuan_by_branch( $config['per_page'] , $page, $this->input->post('q'), $this->input->post('key'), $user_branch);
			}else{
				$clients = $this->clients_pembiayaan_model->get_all_pengajuan( $config['per_page'] , $page, $this->input->post('q'), $this->input->post('key'));
			}			
			
				//ACTIVITY LOG
				$log_data = array(
						'activity_userid' 	    => $this->session->userdata['user_id'],
						'activity_userbranch'   => $this->session->userdata['user_branch'],
						'activity_module' 		=> $this->router->fetch_module(),
						'activity_controller'   => $this->router->fetch_class(),
						'activity_method'       => $this->router->fetch_method(),
						'activity_data'         => 'log_data',
						'activity_remarks'      => 'BROWSE Credit Scoring'
				);
				$log = $this->access_control->log_activity($log_data);						
				//END OF ACTIVITY LOG	
						
						
			$this->template	->set('menu_title', 'Credit Scoring')
							->set('menu_branch', 'active')
							->set('clients', $clients)
							->set('no', $no)
							->set('config', $config)
							->build('creditscoring');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	
}