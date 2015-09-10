<?php

class Target extends Front_Controller{
	
	private $per_page 	= '10';
	private $title 		= 'Target';
	private $module 	= 'target';
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model('officer_model');
		$this->load->model('target_model');
		$this->load->model('target_officer_model');
		$this->load->model('branch_model');
		$this->load->library('pagination');	
	}
	
	public function index($page='0'){
		if($this->session->userdata('logged_in'))
		{
			redirect('target/target_ops', 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	
	// #TARGET
	public function target_ops($page='0'){
		if($this->session->userdata('logged_in'))
		{
			$total_rows = $this->target_model->count_all_target($this->input->post('q'),$this->session->userdata['user_branch']);
			
			//pagination
			$config['base_url']     	= site_url($this->module.'/target_ops');
			$config['total_rows']   	= $total_rows;
			$config['per_page']     	= 15; 
			$config['uri_segment']  	= 3;
			$config['suffix'] 			= '?' . http_build_query($_GET, '', "&");
			$config['first_url'] 		= $config['base_url'] . $config['suffix'];
			$config['num_links'] 		= 2;
			$config['full_tag_open'] 	= '<li>';
			$config['full_tag_close'] 	= '</li>';
			$config['cur_tag_open'] 	= '<li><a href="#"><b>';
			$config['cur_tag_close'] 	= '</b></a></li>';
			$config['num_tag_open'] 	= '<li>';
			$config['num_tag_close'] 	= '</li>';
			$config['first_tag_open'] 	= '<li>';
			$config['first_tag_close'] 	= '</li>';
			$config['last_tag_open'] 	= '<li>';
			$config['last_tag_close'] 	= '</li>';
			$config['next_tag_open'] 	= '<li>';
			$config['next_tag_close'] 	= '</li>';
			$config['prev_tag_open'] 	= '<li>';
			$config['prev_tag_close'] 	= '</li>';
			
			$this->pagination->initialize($config);
			$no =  $this->uri->segment(3);
			
			$target = $this->target_model->get_all( $config['per_page'] ,$page, $this->session->userdata['user_branch']);
			//var_dump($target);			
			$this->template	->set('menu_title', 'Target Operasional')
							->set('menu_branch', 'active')
							->set('target', $target)
							->set('no', $no)
							->set('config', $config)
							->build('target_ops');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}	
	
	public function target_ops_register(){
		if($this->save_target_ops()){
			$this->session->set_flashdata('message', 'success|Target Parameter telah ditambahkan');
			redirect('target/target_ops');
		}
		$officer_list = $this->officer_model->get_list_officer(); //var_dump($officer_list);
		$branch_list  = $this->branch_model->get_all_branch(); //var_dump($branch_list);
			
		$this->template->set('menu_title', 'Add New Target Parameter')
					   ->set('menu_branch', 'active')
					   ->set('officer', $officer_list)
					   ->set('branch', $branch_list)
					   ->set('form_type', 'registration')
				       ->build('target_ops_form');	
		
	}
	
	public function target_ops_edit(){
		
		if($this->save_target_ops()){
			$this->session->set_flashdata('message', 'success|Target Parameter telah diedit');
			redirect('target/target_ops');
			exit;
		}
		//GET SPECIFIC TARGET
		$id     =  $this->uri->segment(3);
		$target =  $this->target_model->get_target_by_id($id);
		$officer_list = $this->officer_model->get_list_officer(); 
		$branch_list  = $this->branch_model->get_all_branch(); 

		$this->template	->set('target', $target)
						->set('menu_title', 'Edit Target Parameter')
						->set('menu_branch', 'active')
						->set('officer', $officer_list)
					    ->set('branch', $branch_list)
						->set('form_type', 'edit')
						->build('target_ops_form');	
	}
	
		
	public function target_ops_delete($id = '0'){
		$tid = $this->uri->segment(3);
			if($this->target_model->delete($tid)){
			   $this->session->set_flashdata('message', 'success|Target Parameter telah dihapus');
				redirect('target/target_ops');
			}
	}	
	
	private function save_target_ops(){
		//set form validation
		$this->form_validation->set_rules('target_category', 'Kategori Target', 'required');
		$this->form_validation->set_rules('target_item', 'Item Target', 'required');
		$this->form_validation->set_rules('target_startdate', 'Tanggal Awal Target', 'required');
		$this->form_validation->set_rules('target_enddate', 'Tanggal Akhir Target', 'required');
	
	
		if($this->form_validation->run() === TRUE){
			$data = array(
					'target_category'   => $this->input->post('target_category'),
					'target_item'       => $this->input->post('target_item'),
					'target_officer'    => $this->session->userdata('user_id'),
					'target_branch'     => $this->input->post('target_branch'),
					'target_amount'     => $this->input->post('target_amount'),
					'target_startdate'	=> $this->input->post('target_startdate'),
					'target_enddate'	=> $this->input->post('target_enddate'),
					'target_remarks'    => $this->input->post('target_remarks')
			);
			$id = $this->input->post('target_id');
			if(!$id){
				return $this->target_model->insert($data);
			}else{
				return $this->target_model->update($id, $data);
			} 
		}
	}

	
	// #TARGET
	public function target_ops_officer($target_id,$page='0'){
		if($this->session->userdata('logged_in'))
		{
			$total_rows = $this->target_officer_model->count_all_target($target_id, $this->session->userdata['user_branch']);
			
			//pagination
			$config['base_url']     	= site_url($this->module.'/target_ops_disposisi');
			$config['total_rows']   	= $total_rows;
			$config['per_page']     	= 15; 
			$config['uri_segment']  	= 3;
			$config['suffix'] 			= '?' . http_build_query($_GET, '', "&");
			$config['first_url'] 		= $config['base_url'] . $config['suffix'];
			$config['num_links'] 		= 2;
			$config['full_tag_open'] 	= '<li>';
			$config['full_tag_close'] 	= '</li>';
			$config['cur_tag_open'] 	= '<li><a href="#"><b>';
			$config['cur_tag_close'] 	= '</b></a></li>';
			$config['num_tag_open'] 	= '<li>';
			$config['num_tag_close'] 	= '</li>';
			$config['first_tag_open'] 	= '<li>';
			$config['first_tag_close'] 	= '</li>';
			$config['last_tag_open'] 	= '<li>';
			$config['last_tag_close'] 	= '</li>';
			$config['next_tag_open'] 	= '<li>';
			$config['next_tag_close'] 	= '</li>';
			$config['prev_tag_open'] 	= '<li>';
			$config['prev_tag_close'] 	= '</li>';
			
			$this->pagination->initialize($config);
			$no =  $this->uri->segment(4);
			
			$target = $this->target_officer_model->get_all( $target_id, $this->session->userdata['user_branch']);
			//var_dump($target);			
			$this->template	->set('menu_title', 'Disposisi Target Operasional')
							->set('menu_branch', 'active')
							->set('target', $target)
							->set('no', $no)
							->set('config', $config)
							->build('target_ops_officer');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}	
	
	
	public function target_ops_disposisi(){
		
		if($this->save_target_disposisi()){
			$this->session->set_flashdata('message', 'success|Disposisi target berhasil');
			redirect('target/target_ops_officer');
			exit;
		}
		//GET SPECIFIC TARGET
		$id     =  $this->uri->segment(3);
		$target =  $this->target_model->get_target_by_id($id);
		$officer_list = $this->officer_model->get_list_officer_by_branch($this->session->userdata['user_branch']); 
		$branch_list  = $this->branch_model->get_all_branch(); 

		$this->template	->set('target', $target)
						->set('menu_title', 'Disposisi Target')
						->set('menu_branch', 'active')
						->set('officer', $officer_list)
					    ->set('branch', $branch_list)
						->set('form_type', 'edit')
						->build('target_ops_disposisi_form');	
	}
	
	public function target_ops_disposisi_edit(){
		
		if($this->save_target_disposisi()){
			$this->session->set_flashdata('message', 'success|Edit disposisi target berhasil');
			redirect('target/target_ops_officer');
			exit;
		}
		//GET SPECIFIC TARGET
		$id     =  $this->uri->segment(3);
		$target =  $this->target_officer_model->get_target_officer_by_id($id);
		$officer_list = $this->officer_model->get_list_officer_by_branch($this->session->userdata['user_branch']); 
		
		$this->template	->set('target', $target)
						->set('menu_title', 'Disposisi Target')
						->set('menu_branch', 'active')
					    ->set('officer', $officer_list)
						->set('form_type', 'edit')
						->build('target_ops_disposisi_form');	
	}
	
	public function target_ops_disposisi_delete($id = '0'){
		$tid = $this->uri->segment(3);
			if($this->target_officer_model->delete($tid)){
			   $this->session->set_flashdata('message', 'success|Disposisi target telah dihapus');
				redirect('target/target_ops');
			}
	}	
	
	private function save_target_disposisi(){
		//set form validation
		$this->form_validation->set_rules('target_officer_officer', 'Field Officer', 'required');
		$this->form_validation->set_rules('target_officer_amount', 'Nilai Target', 'required');
	
	
		if($this->form_validation->run() === TRUE){
			$data = array(
					'target_officer_officer'    => $this->input->post('target_officer_officer'),
					'target_officer_amount'     => $this->input->post('target_officer_amount'),
					'target_officer_target_id'  => $this->input->post('target_id'),
					'target_officer_remarks'    => $this->input->post('target_officer_remarks')
			);
			$id = $this->input->post('target_officer_id');
			if(!$id){
				return $this->target_officer_model->insert($data);
			}else{
				return $this->target_officer_model->update($id, $data);
			} 
		}
	}
}