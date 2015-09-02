<?php
class Clients_unreg extends Front_Controller    {

	private $per_page 	= '30';
	private $title 		= 'Clients_unreg';
	private $module 	= 'clients_unreg';


        public function __construct()
        {
            parent::__construct();
			
			$this->load->library('session');
			$this->load->library('pagination');
        	$this->load->helper('form');
       		$this->load->helper('url');
            $this->load->model('clients_model');
            
        }
        public function index()
        {
            $config['base_url'] = base_url()."index.php/clients_unreg/index";
            $config['total_rows'] = $this->clients_model->count_all($this->input->post('qw'));
            $config['per_page'] = 30;
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
            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
           
			$data['clients'] = $this->clients_model->get_all($config["per_page"],$page,$this->input->post('qw'));
            $data['links'] = $this->pagination->create_links();
			// $this->load->view('index', $data);
			
			//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => '',
							'activity_remarks'      => 'Browse Anggota Keluar'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
					
					
			$this->template	->set('menu_title', 'Data Anggota Keluar')
							->set('menu_client', 'active')
							->set('clients', $data['clients'])
							->set('links', $data['links'])
							->set('config', $config)
							->build('index');

        }

    public function update_status($id)
	{
		
		$this->load->model('clients_model','',TRUE);
		$form_data = array(	'client_status' => $this->input->post('status'), );
		//$primary_key['client_id'] = $this->input->post('client_id');
		    
        $id =  $this->uri->segment(3);
		if ($this->clients_model->update_status($form_data,$id) == TRUE) 
           {
               //ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => $id,
							'activity_remarks'      => 'UPDATE Status Anggota'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
			   $this->session->set_flashdata('flash_message', 'Data berhasil update!'); 
			   $this->session->set_flashdata('flash_status', 'success');
			   redirect('clients_unreg/');
           }
        else
           {
				//ACTIVITY LOG
					$log_data = array(
							'activity_userid' 	    => $this->session->userdata['user_id'],
							'activity_userbranch'   => $this->session->userdata['user_branch'],
							'activity_module' 		=> $this->router->fetch_module(),
							'activity_controller'   => $this->router->fetch_class(),
							'activity_method'       => $this->router->fetch_method(),
							'activity_data'         => $id,
							'activity_remarks'      => 'UPDATE Status Anggota Failed'
					);
					$log = $this->access_control->log_activity($log_data);
					//END OF ACTIVITY LOG	
			   $this->session->set_flashdata('flash_message', 'Data gagal update!'); 
			   $this->session->set_flashdata('flash_status', 'danger');
				 redirect('clients_unreg/');
			}
      
	}
}