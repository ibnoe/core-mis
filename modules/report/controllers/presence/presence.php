<?php

class Presence extends Front_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('branch_model');
		$this->load->model('presence_model');
	}

	public function index(){
		
		if($this->session->userdata('logged_in'))
		{
			//Cek User Login Branch
			$user_branch = $this->session->userdata('user_branch');
			$branch = $this->branch_model->get_all()->result();
			
			$date_start = $this->input->post('date_start');
			$date_end = $this->input->post('date_end');
			
			if(!$date_start AND !$date_end){				
				$date = date("Y-m-d");	
				$date_day = date('l',strtotime($date));	
				if($date_day == "Monday"){
					function week_range($date) {
						$ts = strtotime($date);
						$start = strtotime("-7 day", $ts);
						//$start = date('Y-m-d', $start);	echo $start;
						return array(date('Y-m-d', $start),
									 date('Y-m-d', strtotime('next saturday', $start)));
					}
					list($date_start, $date_end) = week_range($date);
				}else{
					function week_range($date) {
						$ts = strtotime($date);
						$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
						return array(date('Y-m-d', $start),
									 date('Y-m-d', strtotime('next saturday', $start)));
					}
					list($date_start, $date_end) = week_range($date);
				}
			}
			
			//Build
			$this->template	->set('menu_title', 'Presence Report')
							->set('menu_report', 'active')
							->set('branch', $branch)
							->set('date_start', $date_start)
							->set('date_end', $date_end)
							->build('presence/presence');
							
		}else{
			redirect('login', 'refresh');
		}
	}

}