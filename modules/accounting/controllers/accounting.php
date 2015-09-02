<?php

class Accounting extends Front_Controller{
	
	private $per_page 	= '15';
	private $title 		= 'Accounting';
	private $module 	= 'accounting';
	
	public function __construct(){
		parent::__construct();
		$this->load->model('accounting_model');	
		$this->load->model('jurnal_model');		
		$this->load->library('pagination');		
	}
	
	public function index(){
		if($this->session->userdata('logged_in'))
		{
			redirect('accounting/jurnal', 'refresh');
		}
		else
		{
			 //If no session, redirect to login page
			 redirect('login', 'refresh');
		}
	}
	
	public function jurnal($page='0')
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');	
			//if($user_branch == "0"){ $user_branch=NULL;}
			$date_start = $this->input->post('date_start');
			$date_end = $this->input->post('date_end');
			$q = $this->input->post('q');
			$key = $this->input->post('key');
			
			//Get Jurnal
			$total_rows = $this->jurnal_model->count_all_jurnal($this->input->post('q'),$this->input->post('key'),$user_branch, $date_start, $date_end);
			
			
			//pagination
			$config['base_url']     = site_url($this->module.'/jurnal');
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
			
			$jurnal = $this->jurnal_model->get_all_jurnal($config['per_page'] , $page, $this->input->post('q'), $this->input->post('key'), $user_branch, $date_start, $date_end);
			
			//Build
			$this->template	->set('menu_title', 'Jurnal')
							->set('menu_jurnal', 'active')
							->set('jurnal', $jurnal)
							->set('no', $no)
							->set('config', $config)
							->set('date_start', $date_start)
							->set('date_end', $date_end)
							->set('q', $q)
							->set('key', $key)
							->build('jurnal');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}

	public function add()
	{
		if($this->save_jurnal()){
			$this->session->set_flashdata('message', 'success|Jurnal telah ditambahkan');
			redirect($this->module.'/jurnal');
		}
		$account = $this->accounting_model->get_all()->result();
		
		$this->template	->set('data', $data)
						->set('menu_title', 'Tambah Jurnal Baru')
						->set('menu_jurnal', 'active')
						->set('account', $account)
						->build('jurnal_form');	
	}
	
	/*
	public function jurnal_edit()
	{
		if($this->save_jurnal()){
			$this->session->set_flashdata('message', 'success|Jurnal telah diedit');
			redirect($this->module.'/jurnal');
		}
		$id =  $this->uri->segment(3);
		$account = $this->accounting_model->get_all()->result();
		$data = $this->jurnal_model->get_jurnal($id)->result();
		$data = $data[0];
		$this->template	->set('data', $data)
						->set('menu_title', 'Edit Jurnal')
						->set('menu_jurnal', 'active')
						->set('account', $account)
						->build('jurnal_form');	
	}
	
	public function jurnal_delete($id = '0'){
		$id =  $this->uri->segment(3);
			if($this->jurnal_model->delete($id)){
				$this->session->set_flashdata('message', 'success|Jurnal telah dihapus');
				redirect('accounting/jurnal');
				exit;
			}
	}
	*/
	private function save_jurnal()
	{		
		
		//get user ID
		$user_id = $this->session->userdata('user_id');
		$timestamp = date(("Y-m-d H:i:s"));	
		
		//set form validation
		$this->form_validation->set_rules('jurnal_date', 'Tanggal', 'required');
		$this->form_validation->set_rules('jurnal_account_debet', 'Account Debet', 'required');
		$this->form_validation->set_rules('jurnal_nominal', 'Nominal', 'required');
		$this->form_validation->set_rules('jurnal_account_credit', 'Account Kredit', 'required');
		//$this->form_validation->set_rules('jurnal_debet', 'Nominal Debet', 'required');		
		//$this->form_validation->set_rules('jurnal_credit', 'Nominal Kredit', 'required');
		$this->form_validation->set_rules('jurnal_remark', 'Keterangan', 'required');
	
	
		if($this->form_validation->run() === TRUE){
			$id = $this->input->post('jurnal_id');
			$user_branch = $this->session->userdata('user_branch');
			$jurnal_nominal = $this->input->post("jurnal_nominal".$i);
			//$jurnal_nominal = str_replace(".","",$jurnal_nominal);
			
			//process the form
			$data = array(
					'jurnal_date'       	=> $this->input->post('jurnal_date'),
					'jurnal_account_debet' 	=> $this->input->post('jurnal_account_debet'),	
					'jurnal_debet' 			=> $jurnal_nominal,			
					'jurnal_account_credit' => $this->input->post('jurnal_account_credit'),
					'jurnal_credit'	    	=> $jurnal_nominal,
					'jurnal_remark'	    	=> $this->input->post('jurnal_remark'),	
					'jurnal_nobukti_kode'	=> $this->input->post('jurnal_nobukti_kode'),
					'jurnal_nobukti_nomor'	=> $this->input->post('jurnal_nobukti_nomor'),	
					'jurnal_branch'	    	=> $user_branch,
					'created_by'	   		=> $user_id,
					'created_on'	   		=> $timestamp,
					
			);
			$data_update = array(
					'jurnal_date'       	=> $this->input->post('jurnal_date'),
					'jurnal_account_debet' 	=> $this->input->post('jurnal_account_debet'),	
					'jurnal_debet' 			=> $jurnal_nominal,			
					'jurnal_account_credit' => $this->input->post('jurnal_account_credit'),
					'jurnal_credit'	    	=> $jurnal_nominal,
					'jurnal_remark'	    	=> $this->input->post('jurnal_remark'),	
					'jurnal_nobukti_kode'	=> $this->input->post('jurnal_nobukti_kode'),
					'jurnal_nobukti_nomor'	=> $this->input->post('jurnal_nobukti_nomor'),		
					'jurnal_branch'	    	=> $user_branch,
					'modified_by'	   		=> $user_id,
					'modified_on'	   		=> $timestamp,
					
			);
				
			if(!$id){
				return $this->jurnal_model->insert($data);
			}else{
				return $this->jurnal_model->update($id, $data_update);
			} 
		}
	}
	
	//NERACA
	public function neraca()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			if($this->input->post('branch')){
				$user_branch = $this->input->post('branch');
			}
			////if($user_branch == "0"){ $user_branch=NULL;}
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);	 


			$year_start_before = explode("-",$date_start);	 
			$year_start_before = $year_start_before[0];//echo $year_start_before;
			$date_start_before = $year_start_before."-01-01"; 
			$date_start_before = "2013-01-01"; 
			
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						//$account_saldo_before = $account_debet_before - $account_credit_before;
						//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						if($code_level0 == "4"){					
							$account_saldo_before = $account_credit_before -  $account_debet_before;
							$account_saldo = $account_saldo_before + $account_credit - $account_debet;
						}elseif($code_level0 == "5"){
							$account_saldo_before = $account_debet_before - $account_credit_before;
							$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
						}
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$grand_total_pendapatan_saldo = $grand_total_pendapatan_before-$grand_total_pendapatan_debet+$grand_total_pendapatan_credit;
				$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
				//$laba_rugi = ($grand_total_pendapatan_before+$grand_total_pendapatan_credit) - ($grand_total_beban_before+$grand_total_beban_debet);
				$laba_rugi = $grand_total_pendapatan_saldo - $grand_total_beban_saldo;
				$laba_rugi_before = $grand_total_pendapatan_before - $grand_total_beban_before;				
				//End of Hitung Laba Rugi
			
			
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			
			//ASET
			$accounting = $this->accounting_model->get_all_accounting_aset()->result();
			$get_neraca = $this->print_neraca("ASET",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			$grand_total_aktiva_saldo_before = $aset_before;
			$grand_total_aktiva_saldo = $aset_saldo;
			
			//KEWAJIBAN
			$accounting = $this->accounting_model->get_all_accounting_kewajiban()->result();
			$get_neraca = $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			//$neraca .= $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before);
			
			$grand_total_kewajiban_saldo_before = $aset_before;
			$grand_total_kewajiban_saldo = $aset_saldo;
				
			//MODAL
			$accounting = $this->accounting_model->get_all_accounting_modal()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;				
					$account_saldo = $account_saldo_before - $account_debet + $account_credit + $laba_rugi;
					
					/*$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';*/
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td> 								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before ;
					
					$account_saldo = $account_saldo_before - $account_debet + $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;
					
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi_before;
						$account_saldo_before = 0;
						$account_credit = $laba_rugi;
					}elseif($c->accounting_code == "3020001"){
						$account_saldo_before = $account_credit_before - $account_debet_before;
					}
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					  
						$grand_total_modal_debet += $account_debet;
						$grand_total_modal_credit += $account_credit;
						$grand_total_modal_before += $account_saldo_before;					
					
					//if($c->accounting_code == "3020001"){ echo "<b>$account_debet --- $account_credit</b>";}
					
					$neraca .= '<tr>     
								<td align="left" >  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</td>
								<td class="text-right">'.($account_debet < 0 ? "(".number_format(abs($account_debet),2, ',', '.').")" : number_format($account_debet,2, ',', '.')).'</td>
								<td class="text-right">'.($account_credit < 0 ? "(".number_format(abs($account_credit),2, ',', '.').")" : number_format($account_credit,2, ',', '.')).'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
				$grand_total_modal_saldo = $grand_total_modal_before-$grand_total_modal_debet+$grand_total_modal_credit;
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL MODAL</b></td>	
							<td class="text-right"><b>'.($grand_total_modal_before < 0 ? "(".number_format(abs($grand_total_modal_before),2, ',', '.').")" : number_format($grand_total_modal_before,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_debet < 0 ? "(".number_format(abs($grand_total_modal_debet),2, ',', '.').")" : number_format($grand_total_modal_debet,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_credit < 0 ? "(".number_format(abs($grand_total_modal_credit),2, ',', '.').")" : number_format($grand_total_modal_credit,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_saldo < 0 ? "(".number_format(abs($grand_total_modal_saldo),2, ',', '.').")" : number_format($grand_total_modal_saldo,2, ',', '.')).'</b></td>
							</tr>';	
				
				
				
				//-----------	
				//GRAND TOTAL
				//-----------				
				$grand_total_saldo = $grand_total_aktiva_saldo - $grand_total_kewajiban_saldo - $grand_total_modal_saldo;
				$grand_total_before = $grand_total_aktiva_before - $grand_total_kewajiban_before - $grand_total_modal_before;
					$grand_total_before =0;	
					$grand_total_debet =0;	
					$grand_total_credit =0;						
				$neraca .= '<tfoot bgcolor="#ddd"><tr">     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before),2, ',', '.').")" : number_format($grand_total_before,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_debet,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo),2, ',', '.').")" : number_format($grand_total_saldo,2, ',', '.')).'</b></td>
							</tr></tfoot>';	
			
			$this->template	->set('menu_title', 'Neraca')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('neraca');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//LABA RUGI
	public function laba_rugi()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_start);
			//$date_end_before = $date_start;
			$date_end_before = strtotime("-1 day", $date_end_before); 
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2015-01-01";
			//echo $date_start_before." ----- ".$date_end_before; 
			
			$year_start_before = explode("-",$date_start);	 
			$year_start_before = $year_start_before[0];
			//echo $year_start_before;
			$date_start_before = $year_start_before."-01-01"; 
			//$date_start_before = "2013-01-01"; 
			
			
			$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					if($code_level0 == "4"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debit;
					}elseif($code_level0 == "5"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}

					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet,2, ',', '.').'</b></td>
								<td class="text-right"><b>'.number_format($account_credit,2, ',', '.').'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo,2, ',', '.')).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					if($code_level0 == "4"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debit;
					}elseif($code_level0 == "5"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet,2, ',', '.').'</b></td>
								<td class="text-right"><b>'.number_format($account_credit,2, ',', '.').'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					if($code_level0 == "4"){					
						$account_saldo_before = $account_credit_before -  $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}elseif($code_level0 == "5"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
					if($code_level0 == "4"){
						$grand_total_pendapatan_debet += $account_debet;
						$grand_total_pendapatan_credit += $account_credit;
						$grand_total_pendapatan_before += $account_saldo_before;
					}elseif($code_level0 == "5"){
						$grand_total_beban_debet += $account_debet;
						$grand_total_beban_credit += $account_credit;
						$grand_total_beban_before += $account_saldo_before;
					}
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</td>
								<td class="text-right">'.number_format($account_debet,2, ',', '.').'</td>
								<td class="text-right">'.number_format($account_credit,2, ',', '.').'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$grand_total_pendapatan_saldo = $grand_total_pendapatan_before-$grand_total_pendapatan_debet+$grand_total_pendapatan_credit;
				$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
				//$laba_rugi = ($grand_total_pendapatan_before+$grand_total_pendapatan_credit) - ($grand_total_beban_before+$grand_total_beban_debet);
				$laba_rugi = $grand_total_pendapatan_saldo - $grand_total_beban_saldo;
				$neraca .= '<tfoot ><tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL PENDAPATAN</b></td>	
							<td class="text-right"><b>'.($grand_total_pendapatan_before < 0 ? "(".number_format(abs($grand_total_pendapatan_before),2, ',', '.').")" : number_format($grand_total_pendapatan_before,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_pendapatan_debet,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_pendapatan_credit,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.($grand_total_pendapatan_saldo < 0 ? "(".number_format(abs($grand_total_pendapatan_saldo),2, ',', '.').")" : number_format($grand_total_pendapatan_saldo,2, ',', '.')).'</b></td>
							</tr>';	
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL BEBAN</b></td>	
							<td class="text-right"><b>'.($grand_total_beban_before < 0 ? "(".number_format(abs($grand_total_beban_before),2, ',', '.').")" : number_format($grand_total_beban_before,2, ',', '.')).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_beban_debet,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_beban_credit,2, ',', '.').'</b></td>
							<td class="text-right"><b>'.($grand_total_beban_saldo < 0 ? "(".number_format(abs($grand_total_beban_saldo),2, ',', '.').")" : number_format($grand_total_beban_saldo,2, ',', '.')).'</b></td>
							</tr>';	
				$neraca .= '<tr bgcolor="#ddd">     
							<td align="left" ><b>LABA RUGI</b></td>	
							<td class="text-right"></td>
							<td class="text-right"></td>
							<td class="text-right"></td>
							<td class="text-right"><b>'.($laba_rugi < 0 ? "(".number_format(abs($laba_rugi),2, ',', '.').")" : number_format($laba_rugi,2, ',', '.')).'</b></td>
							</tr></tfoot>';	
			
			$this->template	->set('menu_title', 'Laba Rugi')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('labarugi');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	
	//LAPORAN KEUANGAN
	public function laporan_keuangan()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;				
				//End of Hitung Laba Rugi
			
			
			//lap keuangan
			$grand_total_debet =0;
			$grand_total_credit=0;
			$grand_total_before =0;
			$accounting = $this->accounting_model->get_all_accounting()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
									</tr>';					
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi;
					}
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td class="text-right">'.number_format($account_debet).'</td>
								<td class="text-right">'.number_format($account_credit).'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';	
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL				
				$grand_total_saldo = $grand_total_before+$grand_total_debet-$grand_total_credit;
				$neraca .= '<tfoot bgcolor="#ddd"><tr>     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
			
			$this->template	->set('menu_title', 'Laporan Keuangan')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('laporankeuangan');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//NERACA SIMPLE
	public function neraca_simple()
	{
		if($this->session->userdata('logged_in'))
		{
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			$accounting = $this->accounting_model->get_all_accounting()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				
				$saldo_awal =0;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				
				
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';					
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					//Nothing here
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi;
					}
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
							
				}	
				
				//$grand_total_debet += $account_debet;
				//$grand_total_credit += $account_credit;
				
			endforeach; 
			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$neraca .= '<tfoot bgcolor="#ddd"><tr">     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</td>
							<td class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
			
			$this->template	->set('menu_title', 'Neraca Simple')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('neraca');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//GENERAL LEDGER
	public function general_ledger()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						if($code_level0 == "2" OR $code_level0 == "3" OR $code_level0 == "4"){
							$account_saldo_before = $account_credit_before - $account_debet_before;
							$account_saldo = $account_saldo_before + $account_credit - $account_debet;
						}elseif($code_level0 == "5" OR $code_level0 == "1"){
							$account_saldo_before = $account_debet_before - $account_credit_before;
							$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						}
						
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;				
				//End of Hitung Laba Rugi
			
			
			$accounting = $this->accounting_model->get_all_accounting()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					if($code_level0 == "2" OR $code_level0 == "3" OR $code_level0 == "4"){
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}elseif($code_level0 == "5" OR $code_level0 == "1"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					}
						
					$neraca .= '<tr>     
								<td align="left" ><a href="'.site_url($this->module.'/general_ledger_detail/'.$c->accounting_code).'" title="view details"><b>'.$c->accounting_code." ".$c->accounting_name.'</b></a></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2" OR $code_level0 == "3" OR $code_level0 == "4"){
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}elseif($code_level0 == "5" OR $code_level0 == "1"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					}
						
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.site_url($this->module.'/general_ledger_detail/'.$c->accounting_code).'" title="view details">'.$c->accounting_code." ".$c->accounting_name.'</a></b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{		
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2" OR $code_level0 == "3" OR $code_level0 == "4"){
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}elseif($code_level0 == "5" OR $code_level0 == "1"){
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					}
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi;
					}
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;	
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.site_url($this->module.'/general_ledger_detail/'.$c->accounting_code).'" title="view details"><b>'.$c->accounting_code." ".$c->accounting_name.'</b></a></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$neraca .= '<tfoot bgcolor="#ddd"><tr>     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
			
			$this->template	->set('menu_title', 'General Ledger')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('general_ledger');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}

	//GENERAL LEDGER DETAIL
	public function general_ledger_detail($page='0')
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start = "2013-01-01";
				$date_end = $date_today;			
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);	  		
			$date_start_before = "2013-01-01";
			
			
			$account_no =  $this->uri->segment(3);
			$account_name = $this->accounting_model->get_account($account_no)->result();			
			$account_name = $account_name[0]->accounting_name;
			
			$total_rows = $this->jurnal_model->count_all_jurnal_by_account($account_no,$user_branch, $date_start,$date_end);
			
			
			$no =  $this->uri->segment(4);
			$page =  $this->uri->segment(4);
			$jurnal = $this->jurnal_model->get_all_jurnal_by_account($account_no, $user_branch, $date_start,$date_end);
			foreach($jurnal as $c){  
							
				if($c->jurnal_account_debet == $account_no) { $saldo_debet = $c->jurnal_debet; }else{ $saldo_debet = 0; }
				if($c->jurnal_account_credit == $account_no) { $saldo_credit = $c->jurnal_credit; }else{ $saldo_credit = 0; }
				$saldo = $saldo_debet - $saldo_credit;
				
				$total_saldo += $saldo; 
			}
			
			$this->template	->set('menu_title', 'General Ledger ')
							->set('menu_jurnal', 'active')
							->set('jurnal', $jurnal)
							->set('no', $no)
							->set('account_no', $account_no)
							->set('total_saldo', $total_saldo)
							->set('account_name', $account_name)
							->set('total_rows', $total_rows)
							->set('config', $config)
							->build('general_ledger_detail');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}

	//GENERAL LEDGER DETAIL
	public function general_ledger_detail_download($page='0')
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			
			
			//load our new PHPExcel library
			$this->load->library('excel');
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start = "2013-01-01";
				$date_end = $date_today;			
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);	  		
			$date_start_before = "2013-01-01";
			
			
			$account_no =  $this->uri->segment(3);
			$account_name = $this->accounting_model->get_account($account_no)->result();			
			$account_name = $account_name[0]->accounting_name;
			
			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()->setCreator("Amartha MIS");
			$objPHPExcel->getProperties()->setLastModifiedBy("Amartha MIS");
			$objPHPExcel->getProperties()->setTitle("GENERAL LEDGER");
			$objPHPExcel->getProperties()->setSubject("GENERAL LEDGER");
			$objPHPExcel->getProperties()->setDescription("GENERAL LEDGER");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('GENERAL LEDGER');
			
			//TITLE
			$objPHPExcel->getActiveSheet()->setCellValue("A1", "Amartha Microfinance");
			$objPHPExcel->getActiveSheet()->setCellValue("A2", "GENERAL LEDGER ($account_name / $account_no)");
			$objPHPExcel->getActiveSheet()->mergeCells("A1:E1");
			$objPHPExcel->getActiveSheet()->mergeCells("A2:E2");
			$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));
			$objPHPExcel->getActiveSheet()->getStyle("A2")->applyFromArray(array("font" => array( "bold" => true)));
			//TOP ROW
			$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array("font" => array( "bold" => true)));			
			$objPHPExcel->getActiveSheet()->setCellValue("A4", "TANGGAL");
			$objPHPExcel->getActiveSheet()->setCellValue("B4", "DESKRIPSI");
			$objPHPExcel->getActiveSheet()->setCellValue("C4", "DEBET");
			$objPHPExcel->getActiveSheet()->setCellValue("D4", "KREDIT");
			$objPHPExcel->getActiveSheet()->setCellValue("E4", "SALDO");
			$objPHPExcel->getActiveSheet()->getStyle("C4:E4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			
			$no=5;
			$jurnal = $this->jurnal_model->get_all_jurnal_by_account($account_no, $user_branch, $date_start,$date_end);
			$total_saldo = 0;
			foreach($jurnal AS $c){
			
						if($account_no == "1010004"){ 
							$kk = "$c->jurnal_remark" ;
							$get_kaskecil = $this->jurnal_model->get_kaskecil_detail($kk)->result();
							$kaskecil_detail = $get_kaskecil[0]->kaskecil_remark;
						}else{
							$kaskecil_detail = NULL;
						}								
							
						if($c->jurnal_account_debet == $account_no) { $saldo_debet = $c->jurnal_debet; }else{ $saldo_debet = 0; }
						if($c->jurnal_account_credit == $account_no) { $saldo_credit = $c->jurnal_credit; }else{ $saldo_credit = 0; }
						$saldo += ($saldo_debet - $saldo_credit);
				
				$objPHPExcel->getActiveSheet()->setCellValue("A$no", $c->jurnal_date);
				$objPHPExcel->getActiveSheet()->setCellValue("B$no", $c->jurnal_remark);
				$objPHPExcel->getActiveSheet()->setCellValue("C$no", $saldo_debet);
				$objPHPExcel->getActiveSheet()->setCellValue("D$no", $saldo_credit);
				$objPHPExcel->getActiveSheet()->setCellValue("E$no", $saldo);
				$no++;
			}
			//Set Column Auto Width
			foreach(range('A','E') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
			}
			//EXPORT	
			$filename = "GL_".$account_no."_" . time() . '.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	
	private function print_neraca($label,$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$branch)
	{
		foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet,2, ',', '.').'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit,2, ',', '.').'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet,2, ',', '.').'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit,2, ',', '.').'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
						$grand_total_aktiva_debet += $account_debet;
						$grand_total_aktiva_credit += $account_credit;
						$grand_total_aktiva_before += $account_saldo_before;					
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td align="right" class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before),2, ',', '.').")" : number_format($account_saldo_before,2, ',', '.')).'</td>
								<td align="right" class="text-right">'.number_format($account_debet,2, ',', '.').'</td>
								<td align="right" class="text-right">'.number_format($account_credit,2, ',', '.').'</td>
								<td align="right" class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo),2, ',', '.').")" : number_format($account_saldo,2, ',', '.')).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit; 

				if($code_level0 == "2"){					
					$grand_total_aktiva_saldo = $grand_total_aktiva_before-$grand_total_aktiva_debet+$grand_total_aktiva_credit;
				}else{
					$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit;				
				}				
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL '.$label.'</b></td>	
							<td align="right" class="text-right"><b>'.($grand_total_aktiva_before < 0 ? "(".number_format(abs($grand_total_aktiva_before),2, ',', '.').")" : number_format($grand_total_aktiva_before,2, ',', '.')).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_aktiva_debet,2, ',', '.').'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_aktiva_credit,2, ',', '.').'</b></td>
							<td align="right" class="text-right"><b>'.($grand_total_aktiva_saldo < 0 ? "(".number_format(abs($grand_total_aktiva_saldo),2, ',', '.').")" : number_format($grand_total_aktiva_saldo,2, ',', '.')).'</b></td>
							</tr>';
							
			return array($neraca, $grand_total_aktiva_before, $grand_total_aktiva_debet, $grand_total_aktiva_credit,$grand_total_aktiva_saldo);
	}
	
	private function print_neraca_excel($label,$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$branch)
	{
		foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
						$grand_total_aktiva_debet += $account_debet;
						$grand_total_aktiva_credit += $account_credit;
						$grand_total_aktiva_before += $account_saldo_before;					
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td align="right" class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td align="right" class="text-right">'.number_format($account_debet).'</td>
								<td align="right" class="text-right">'.number_format($account_credit).'</td>
								<td align="right" class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit;				
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL '.$label.'</b></td>	
							<td align="right" class="text-right"><b>'.($grand_total_aktiva_before < 0 ? "(".number_format(abs($grand_total_aktiva_before)).")" : number_format($grand_total_aktiva_before)).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_aktiva_debet).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_aktiva_credit).'</b></td>
							<td align="right" class="text-right"><b>'.($grand_total_aktiva_saldo < 0 ? "(".number_format(abs($grand_total_aktiva_saldo)).")" : number_format($grand_total_aktiva_saldo)).'</b></td>
							</tr>';
							
			return array($neraca, $grand_total_aktiva_before, $grand_total_aktiva_debet, $grand_total_aktiva_credit,$grand_total_aktiva_saldo);
	}
	
	//DOWNLOAD NERACA
	public function download_neraca()
	{
		if($this->session->userdata('logged_in'))
		{
			$timestamp=date("Ymdhis");
			$tgl=date("d-M-Y");
			$filename="NERACA_$timestamp";	
			$html = "<style> table tr td,table thead tr td, table tr th{ border-left:0; border-right:0;} table thead tr td,table thead tr th,table tr th{ border-bottom: 2px solid #000; }</style>";
			$html .= '';
			//$html .= '<h1 align="center">Amartha Microfinance</h1>';
			//$html .= '<hr/>';
			$html .= '<h2 align="center">NERACA SALDO</h2><br/>';
			$html .= '<table border="1" width="100%">';
			////$html .= '<thead>';                 
			$html .= '<tr>';
			$html .= '<th>Account</th>';
			$html .= '<th align="right" class="text-right">Saldo Awal</th>';
			$html .= '<th align="right" class="text-right">Debet</th>';
			$html .= '<th align="right" class="text-right">Credit</th>';
			$html .= '<th align="right" class="text-right">Saldo Akhir</th>';
			$html .= '</tr> ';                 
			//$html .= '</thead>';
			
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;				
				//End of Hitung Laba Rugi
			
			
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			
			//ASET
			$accounting = $this->accounting_model->get_all_accounting_aset()->result();
			$get_neraca = $this->print_neraca("ASET",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			
			
			//KEWAJIBAN
			$accounting = $this->accounting_model->get_all_accounting_kewajiban()->result();
			$get_neraca = $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			//$neraca .= $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before);
			
				
			//MODAL
			$accounting = $this->accounting_model->get_all_accounting_modal()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$account_saldo_before = $account_credit_before - $account_debet_before;
					$account_saldo = $account_saldo_before - $account_debet + $account_credit + $laba_rugi;
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					$account_saldo_before = $account_credit_before - $account_debet_before;
					$account_saldo = $account_saldo_before - $account_debet + $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;
					
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi_before;
						$account_saldo_before = 0;
						$account_credit = $laba_rugi;
					}elseif($c->accounting_code == "3020001"){
						$account_saldo_before = $account_debet_before - $account_credit_before ;
					}
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					  
						$grand_total_modal_debet += $account_debet;
						$grand_total_modal_credit += $account_credit;
						$grand_total_modal_before += $account_saldo_before;					
					
					
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td align="right" class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td align="right" class="text-right">'.number_format($account_debet).'</td>
								<td align="right" class="text-right">'.number_format($account_credit).'</td>
								<td align="right" class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
				$grand_total_modal_saldo = $grand_total_modal_before+$grand_total_modal_debet-$grand_total_modal_credit;
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL MODAL</b></td>	
							<td align="right" class="text-right"><b>'.($grand_total_pasiva_before < 0 ? "(".number_format(abs($grand_total_pasiva_before)).")" : number_format($grand_total_pasiva_before)).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_pasiva_debet).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_pasiva_credit).'</b></td>
							<td align="right" class="text-right"><b>'.($grand_total_pasiva_saldo < 0 ? "(".number_format(abs($grand_total_pasiva_saldo)).")" : number_format($grand_total_pasiva_saldo)).'</b></td>
							</tr>';	
				
				
				
				//-----------	
				//GRAND TOTAL
				//-----------				
				$grand_total_saldo = $grand_total_aktiva_saldo - $grand_total_kewajiban_saldo - $grand_total_modal_saldo;
				$grand_total_before = $grand_total_aktiva_before - $grand_total_kewajiban_before - $grand_total_modal_before;
							
				$neraca .= '<tfoot bgcolor="#ddd"><tr>     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td align="right" class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td align="right" class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot></table>';	
							
			$html .= $neraca;
			$this->load->library('mpdf');
			$mpdf=new mPDF('utf-8', 'A4-L');
			$mpdf->SetHeader("Amartha Microfinance".'||'.$tgl.'|'); 
			$mpdf->SetFooter("Neraca Saldo".'||{PAGENO}|'); 
			$mpdf->WriteHTML($html);
			//echo $html;
			//$this->mpdf->Output();
			$pdfFilePath = FCPATH."downloads/neraca/$filename.pdf";
			$pdffile = base_url()."downloads/neraca/$filename.pdf";
			$mpdf->Output($pdfFilePath,'F');
			
			redirect($pdffile, 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//DOWNLOAD LABARUGI
	public function download_labarugi()
	{
		if($this->session->userdata('logged_in'))
		{
			$timestamp=date("Ymdhis");
			$tgl=date("d-M-Y");
			$filename="LAPORAN_LABARUGI_$timestamp";	
			$html = "<style> table tr td,table thead tr td, table tr th{ border-left:0; border-right:0;} table thead tr td,table thead tr th,table tr th{ border-bottom: 2px solid #000; }</style>";
			$html .= '';
			//$html .= '<h1 align="center">Amartha Microfinance</h1>';
			//$html .= '<hr/>';
			$html .= '<h2 align="center">LAPORAN LABA RUGI</h2><br/>';
			$html .= '<table border="1" width="100%">';
			//$html .= '<thead>';                 
			$html .= '<tr>';
			$html .= '<th>Account</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Awal</th>';
			$html .= '<th align="right" class="text-right" width="100px">Debet</th>';
			$html .= '<th align="right" class="text-right" width="100px">Credit</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Akhir</th>';
			$html .= '</tr> ';                 
			$html .= '</thead>';
			
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
					if($code_level0 == "4"){
						$grand_total_pendapatan_debet += $account_debet;
						$grand_total_pendapatan_credit += $account_credit;
						$grand_total_pendapatan_before += $account_saldo_before;
					}elseif($code_level0 == "5"){
						$grand_total_beban_debet += $account_debet;
						$grand_total_beban_credit += $account_credit;
						$grand_total_beban_before += $account_saldo_before;
					}
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td class="text-right">'.number_format($account_debet).'</td>
								<td class="text-right">'.number_format($account_credit).'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
				$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
				$laba_rugi = ($grand_total_pendapatan_before+$grand_total_pendapatan_credit) - ($grand_total_beban_before+$grand_total_beban_debet);
				
				$neraca .= '<tfoot ><tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL PENDAPATAN</b></td>	
							<td class="text-right"><b>'.($grand_total_pendapatan_before < 0 ? "(".number_format(abs($grand_total_pendapatan_before)).")" : number_format($grand_total_pendapatan_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_pendapatan_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_pendapatan_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_pendapatan_saldo < 0 ? "(".number_format(abs($grand_total_pendapatan_saldo)).")" : number_format($grand_total_pendapatan_saldo)).'</b></td>
							</tr>';	
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL BEBAN</b></td>	
							<td class="text-right"><b>'.($grand_total_beban_before < 0 ? "(".number_format(abs($grand_total_beban_before)).")" : number_format($grand_total_beban_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_beban_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_beban_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_beban_saldo < 0 ? "(".number_format(abs($grand_total_beban_saldo)).")" : number_format($grand_total_beban_saldo)).'</b></td>
							</tr>';	
				$neraca .= '<tr bgcolor="#ddd">     
							<td align="left" ><b>LABA RUGI</b></td>	
							<td class="text-right"></td>
							<td class="text-right"></td>
							<td class="text-right"></td>
							<td class="text-right"><b>'.($laba_rugi < 0 ? "(".number_format(abs($laba_rugi)).")" : number_format($laba_rugi)).'</b></td>
							</tr></tfoot>';	
				$neraca .= '</table>';
			
			$html .= $neraca;
			$this->load->library('mpdf');
			$mpdf=new mPDF('utf-8', 'A4-L');
			$mpdf->SetHeader("Amartha Microfinance".'||'.$tgl.'|'); 
			$mpdf->SetFooter("Laporan Laba Rugi".'||{PAGENO}|'); 
			$mpdf->WriteHTML($html);
			//echo $html;
			//$this->mpdf->Output();
			$pdfFilePath = FCPATH."downloads/labarugi/$filename.pdf";
			$pdffile = base_url()."downloads/labarugi/$filename.pdf";
			$mpdf->Output($pdfFilePath,'F');
			
			redirect($pdffile, 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//DOWNLOAD LAPORANKEUNGAN
	public function download_laporankeuangan()
	{
		if($this->session->userdata('logged_in'))
		{
			$timestamp=date("Ymdhis");
			$tgl=date("d-M-Y");
			$filename="LAPORAN_KEUANGAN_$timestamp";	
			$html = "<style> table tr td,table thead tr td, table tr th{ border-left:0; border-right:0;} table thead tr td,table thead tr th,table tr th{ border-bottom: 2px solid #000; }</style>";
			$html .= '';
			//$html .= '<h1 align="center">Amartha Microfinance</h1>';
			//$html .= '<hr/>';
			$html .= '<h2 align="center">LAPORAN KEUANGAN</h2><br/>';
			$html .= '<table border="1" width="100%">';
			//$html .= '<thead>';                 
			$html .= '<tr>';
			$html .= '<th>Account</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Awal</th>';
			$html .= '<th align="right" class="text-right" width="100px">Debet</th>';
			$html .= '<th align="right" class="text-right" width="100px">Credit</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Akhir</th>';
			$html .= '</tr> ';                 
			$html .= '</thead>';
			

			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;				
				//End of Hitung Laba Rugi
			
			
			//lap keuangan
			$grand_total_debet =0;
			$grand_total_credit=0;
			$grand_total_before =0;
			$accounting = $this->accounting_model->get_all_accounting()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
									</tr>';					
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi;
					}
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td class="text-right">'.number_format($account_debet).'</td>
								<td class="text-right">'.number_format($account_credit).'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';	
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL				
				$grand_total_saldo = $grand_total_before+$grand_total_debet-$grand_total_credit;
				$neraca .= '<tfoot bgcolor="#ddd"><tr>     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
							
			$neraca .= '</table>';
			
			$html .= $neraca;
			$this->load->library('mpdf');
			$mpdf=new mPDF('utf-8', 'A4-L');
			$mpdf->SetHeader("Amartha Microfinance".'||'.$tgl.'|'); 
			$mpdf->SetFooter("Laporan keuangan".'||{PAGENO}|'); 
			$mpdf->WriteHTML($html);
			//echo $html;
			//$this->mpdf->Output();
			$pdfFilePath = FCPATH."downloads/laporankeuangan/$filename.pdf";
			$pdffile = base_url()."downloads/laporankeuangan/$filename.pdf";
			$mpdf->Output($pdfFilePath,'F');
			
			redirect($pdffile, 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//DOWNLOAD GL
	public function download_gl()
	{
		if($this->session->userdata('logged_in'))
		{
			$timestamp=date("Ymdhis");
			$tgl=date("d-M-Y");
			$filename="GENERAL_LEDGER_$timestamp";	
			$html = "<style> table tr td,table thead tr td, table tr th{ border-left:0; border-right:0;} table thead tr td,table thead tr th,table tr th{ border-bottom: 2px solid #000; }</style>";
			$html .= '';
			//$html .= '<h1 align="center">Amartha Microfinance</h1>';
			//$html .= '<hr/>';
			$html .= '<h2 align="center">GENERAL LEDGER</h2><br/>';
			$html .= '<table border="1" width="100%">';
			//$html .= '<thead>';                 
			$html .= '<tr>';
			$html .= '<th>Account</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Awal</th>';
			$html .= '<th align="right" class="text-right" width="100px">Debet</th>';
			$html .= '<th align="right" class="text-right" width="100px">Credit</th>';
			$html .= '<th align="right" class="text-right" width="100px">Saldo Akhir</th>';
			$html .= '</tr> ';                 
			$html .= '</thead>';
			

			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;				
				//End of Hitung Laba Rugi
			
			
			$accounting = $this->accounting_model->get_all_accounting()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{		
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi;
					}
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;	
					
					$neraca .= '<tr>     
								<td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td align="right" class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td align="right" class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td align="right" class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}	
				$code_level0_old = $code_level0;
			endforeach; 
			
				//GRAND TOTAL
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$neraca .= '<tfoot bgcolor="#ddd"><tr>     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td align="right"  class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td align="right" class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td align="right" class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
			
							
			$neraca .= '</table>';
			
			$html .= $neraca;
			$this->load->library('mpdf');
			$mpdf=new mPDF('utf-8', 'A4-L');
			$mpdf->SetHeader("Amartha Microfinance".'||'.$tgl.'|'); 
			$mpdf->SetFooter("General Ledger".'||{PAGENO}|'); 
			$mpdf->WriteHTML($html);
			//echo $html;
			//$this->mpdf->Output();
			$pdfFilePath = FCPATH."downloads/generalledger/$filename.pdf";
			$pdffile = base_url()."downloads/generalledger/$filename.pdf";
			$mpdf->Output($pdfFilePath,'F');
			
			redirect($pdffile, 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	private function get_account_saldo($accounting_code,$level,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch)
	{
				$code = $accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($level == "1"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
			
				}elseif($level == "2"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
				}elseif($level == "3"){	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;					
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
									
					
				}				
					$grand_total_debet 	= $account_debet;
					$grand_total_credit = $account_credit;
					$grand_total_before = $account_saldo_before;						
					$grand_total_saldo 	=  $grand_total_before+$grand_total_debet-$grand_total_credit;				
				
			return array($grand_total_before, $grand_total_debet, $grand_total_credit, $grand_total_saldo);
	}
	
	
	//LAPORAN ARUS KAS
	public function laporan_arus_kas()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				//$date_start =$week_today[0];
				//$date_end = $week_today[1];	
				$date_start = $date_year_today."-01-01";	
				$date_end 	= $date_today;
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			$arus_kas_operasi_actual = 0;
			$arus_kas_operasi_before = 0;
			
				//Hitung Laba Rugi TODAY
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
							
							//*							
							$grand_total_pendapatan_credit_before += $account_credit;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
							
							//*							
							$grand_total_beban_debet_before += $account_debet;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;			
					$laba_rugi_before = $grand_total_pendapatan_credit_before - $grand_total_beban_debet_before;			
				//End of Hitung Laba Rugi
			
			//ARUS KAS OPERASI	
				$arus_kas_operasi_actual += $laba_rugi;
				$arus_kas_operasi_before += $laba_rugi_before;
				
				$grand_total_debet=0;
				$grand_total_credit=0;
				$grand_total_before=0;
				
				//Penyusutan aset tetap
				$get_account_saldo = $this->get_account_saldo("1060302",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$actual_1060302 = $total_saldo; 
					$before_1060302 = $total_before;
				$get_account_saldo = $this->get_account_saldo("1060202",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$actual_1060202 = $total_saldo;
					$before_1060202 = $total_before;			
				$penyusutan_aset_tetap_actual = $actual_1060302 + $actual_1060202;
				$penyusutan_aset_tetap_before = $before_1060302 + $before_1060202;
				$arus_kas_operasi_actual += $penyusutan_aset_tetap_actual;
				$arus_kas_operasi_before += $penyusutan_aset_tetap_before;
				
				//Piutang Pembiayaan
				$get_account_saldo = $this->get_account_saldo("1030000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$piutang_pembiayaan_actual = $total_saldo;
					$piutang_pembiayaan_before = $total_before;
					$arus_kas_operasi_actual += $piutang_pembiayaan_actual;
					$arus_kas_operasi_before += $piutang_pembiayaan_before;
				
				//Beban dibayar di muka
				$get_account_saldo = $this->get_account_saldo("1070104",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$beban_dibayar_dimuka_actual = $total_saldo;
					$beban_dibayar_dimuka_before = $total_before;
					$arus_kas_operasi_actual += $beban_dibayar_dimuka_actual;
					$arus_kas_operasi_before += $beban_dibayar_dimuka_before;
					
				//persediaan_barang_cetakan
				$get_account_saldo = $this->get_account_saldo("1070104",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$persediaan_barang_cetakan_actual = $total_saldo;
					$persediaan_barang_cetakan_before = $total_before;
					$arus_kas_operasi_actual += $persediaan_barang_cetakan_actual;
					$arus_kas_operasi_before += $persediaan_barang_cetakan_before;
					
				//simpanan anggota
				$get_account_saldo = $this->get_account_saldo("2010000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_anggota_actual = $total_saldo;
					$simpanan_anggota_before = $total_before;
					$arus_kas_operasi_actual += $simpanan_anggota_actual;
					$arus_kas_operasi_before += $simpanan_anggota_before;
				
				//simpanan berjangka
				$get_account_saldo = $this->get_account_saldo("2020000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_berjangka_actual = $total_saldo;
					$simpanan_berjangka_before = $total_before;
					$arus_kas_operasi_actual += $simpanan_berjangka_actual;
					$arus_kas_operasi_before += $simpanan_berjangka_before;
					
				//hutang pembiayaan
				$get_account_saldo = $this->get_account_saldo("2040000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$hutang_pembiayaan_actual = $total_saldo;
					$hutang_pembiayaan_before = $total_before;
					$arus_kas_operasi_actual += $hutang_pembiayaan_actual;
					$arus_kas_operasi_before += $hutang_pembiayaan_before;
									
				//hutang_lain
				$get_account_saldo = $this->get_account_saldo("2050000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$hutang_lain_actual = $total_saldo;
					$hutang_lain_before = $total_before;
					$arus_kas_operasi_actual += $hutang_lain_actual;
					$arus_kas_operasi_before += $hutang_lain_before;
				
				
			//ARUS KAS INVESTASI
			$arus_kas_investasi_actual = 0;
			$arus_kas_investasi_before = 0;
				
				//Penambahan aset tetap
				$get_account_saldo = $this->get_account_saldo("1060000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_aset_tetap_actual = $total_saldo;
					$penambahan_aset_tetap_before = $total_before;
					$arus_kas_investasi_actual += $penambahan_aset_tetap_actual;
					$arus_kas_investasi_before += $penambahan_aset_tetap_before;
					
				//Penambahan aset lain
				$get_account_saldo = $this->get_account_saldo("1070000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_aset_lain_actual = $total_saldo;
					$penambahan_aset_lain_before = $total_before;
					$arus_kas_investasi_actual += $penambahan_aset_lain_actual;
					$arus_kas_investasi_before += $penambahan_aset_lain_before;
				
			//ARUS KAS PENDANAAN
			$arus_kas_pendanaan_actual = 0;
			$arus_kas_pendanaan_before = 0;
				
				//Penambahan simpanan
				$get_account_saldo = $this->get_account_saldo("3010101",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_simpanan_3010101_actual = $total_saldo;
					$penambahan_simpanan_3010101_before = $total_before;
				$get_account_saldo = $this->get_account_saldo("3010102",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_simpanan_3010102_actual = $total_saldo;
					$penambahan_simpanan_3010102_before = $total_before;
					$penambahan_simpanan_actual = $penambahan_simpanan_3010101_actual + $penambahan_simpanan_3010102_actual;
					$penambahan_simpanan_before = $penambahan_simpanan_3010101_before + $penambahan_simpanan_3010102_before;				
					$arus_kas_pendanaan_actual += $penambahan_simpanan_actual;
					$arus_kas_pendanaan_before += $penambahan_simpanan_before;
				
				//Penambahan hibah			
				$get_account_saldo = $this->get_account_saldo("3010103",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_hibah_actual = $total_saldo;
					$penambahan_hibah_before = $total_before;
					$arus_kas_pendanaan_actual += $penambahan_hibah_actual;
					$arus_kas_pendanaan_before += $penambahan_hibah_before;
					
				//Penambahan modal			
				$get_account_saldo = $this->get_account_saldo("3010202",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_modal_actual = $total_saldo;
					$penambahan_modal_before = $total_before;
					$arus_kas_pendanaan_actual += $penambahan_modal_actual;
					$arus_kas_pendanaan_before += $penambahan_modal_before;
					
			//KAS AWAL TAHUN
			$kas_awal_tahun_actual = $arus_kas_operasi_actual + $arus_kas_investasi_actual + $arus_kas_pendanaan_actual;
			$kas_awal_tahun_before = $arus_kas_operasi_before + $arus_kas_investasi_before + $arus_kas_pendanaan_before;
			
			//KAS AKHIR TAHUN		
			$get_account_saldo = $this->get_account_saldo("1010000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
			list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
				$kas_akhir_tahun_actual = $total_saldo;
				$kas_akhir_tahun_before = $total_before;
			
			//SELISIH KAS
			$selisih_kas_actual = $kas_awal_tahun_actual - $kas_akhir_tahun_actual;
			$selisih_kas_before = $kas_awal_tahun_before - $kas_akhir_tahun_before;
			
			//LAPORAN FORMAT
				$laporan .= '<table cellpadding="5px" width="95%" align="center">';
				$laporan .= '<tr>
							<td width="28%" align="left" ></td>	
							<td width="20%" align="center" style="border-bottom: 1px solid #000;">2014</td>							
							<td width="4%" align="left" ></td>	
							<td width="20%" align="center" style="border-bottom: 1px solid #000;">2013</td>
							<td width="28%" align="right"></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><b>Arus Kas Dari Aktivitas Operasi</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><b><i>Cash Flow From Operating Activities</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Laba bersih</td>	
							<td align="right">'.($laba_rugi < 0 ? "(".number_format(abs($laba_rugi)).")" : number_format($laba_rugi)).'</td>							
							<td>&nbsp;</td>
							<td align="right">'.($laba_rugi_before < 0 ? "(".number_format(abs($laba_rugi_before)).")" : number_format($laba_rugi_before)).'</td>
							<td align="right"><i>Net Income</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyesuaian untuk merekonsiliasi Laba bersih menjadi arus kas bersih dari aktivitas operasi:</td>	
							<td align="right"> </td>
							<td>&nbsp;</td>
							<td align="right"> </td>
							<td align="right"><i>Adjustment to reconcile net income to be net cash from Operationg activities:</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyusutan aset tetap</td>	
							<td align="right">'.($penyusutan_aset_tetap_actual < 0 ? "(".number_format(abs($penyusutan_aset_tetap_actual)).")" : number_format($penyusutan_aset_tetap_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penyusutan_aset_tetap_before < 0 ? "(".number_format(abs($penyusutan_aset_tetap_before)).")" : number_format($penyusutan_aset_tetap_before)).'</td>
							<td align="right"><i>Depreciation of fixed assets</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyusutan Aset dan Liabilitas dari Operasional: </td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><i>Changes in operating assets and liablities</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Piutang Pembiayaan</td>	
							<td align="right">'.($piutang_pembiayaan_actual < 0 ? "(".number_format(abs($piutang_pembiayaan_actual)).")" : number_format($piutang_pembiayaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($piutang_pembiayaan_before < 0 ? "(".number_format(abs($piutang_pembiayaan_before)).")" : number_format($piutang_pembiayaan_before)).'</td>
							<td align="right"><i>Financing Receivables</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Beban dibayar dimuka</td>	
							<td align="right">'.($beban_dibayar_dimuka_actual < 0 ? "(".number_format(abs($beban_dibayar_dimuka_actual)).")" : number_format($beban_dibayar_dimuka_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($beban_dibayar_dimuka_before < 0 ? "(".number_format(abs($beban_dibayar_dimuka_before)).")" : number_format($beban_dibayar_dimuka_before)).'</td>
							<td align="right"><i>Prepaid Expenses</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Persediaan Barang Cetakan</td>	
							<td align="right">'.($persediaan_barang_cetakan_actual < 0 ? "(".number_format(abs($persediaan_barang_cetakan_actual)).")" : number_format($persediaan_barang_cetakan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($persediaan_barang_cetakan_before < 0 ? "(".number_format(abs($persediaan_barang_cetakan_before)).")" : number_format($persediaan_barang_cetakan_before)).'</td>
							<td align="right"><i>Inventory</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Simpanan Anggota</td>	
							<td align="right">'.($simpanan_anggota_actual < 0 ? "(".number_format(abs($simpanan_anggota_actual)).")" : number_format($simpanan_anggota_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($simpanan_anggota_before < 0 ? "(".number_format(abs($simpanan_anggota_before)).")" : number_format($simpanan_anggota_before)).'</td>
							<td align="right"><i>Members Savings</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Simpanan Berjangka</td>	
							<td align="right">'.($simpanan_berjangka_actual < 0 ? "(".number_format(abs($simpanan_berjangka_actual)).")" : number_format($simpanan_berjangka_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($simpanan_berjangka_before < 0 ? "(".number_format(abs($simpanan_berjangka_before)).")" : number_format($simpanan_berjangka_before)).'</td>
							<td align="right"><i>Term Deposits</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Hutang Pembiayaan</td>	
							<td align="right">'.($hutang_pembiayaan_actual < 0 ? "(".number_format(abs($hutang_pembiayaan_actual)).")" : number_format($hutang_pembiayaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($hutang_pembiayaan_before < 0 ? "(".number_format(abs($hutang_pembiayaan_before)).")" : number_format($hutang_pembiayaan_before)).'</td>
							<td align="right"><i>Financing Payables</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Hutang Lain-lain</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($hutang_lain_actual < 0 ? "(".number_format(abs($hutang_lain_actual)).")" : number_format($hutang_lain_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($hutang_lain_before < 0 ? "(".number_format(abs($hutang_lain_before)).")" : number_format($hutang_lain_before)).'</td>
							<td align="right"><i>Other Liabilities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas operasi</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_operasi_actual < 0 ? "(".number_format(abs($arus_kas_operasi_actual)).")" : number_format($arus_kas_operasi_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_operasi_before < 0 ? "(".number_format(abs($arus_kas_operasi_before)).")" : number_format($arus_kas_operasi_before)).'</td>
							<td align="right"><i>Net cash flow from operating activities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Arus Kas Dari Aktivitas Investasi</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><br/><b><i>Cash Flow From Investment Operating</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan Aset Tetap</td>	
							<td align="right">'.($penambahan_aset_tetap_actual < 0 ? "(".number_format(abs($penambahan_aset_tetap_actual)).")" : number_format($penambahan_aset_tetap_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_aset_tetap_before < 0 ? "(".number_format(abs($penambahan_aset_tetap_before)).")" : number_format($penambahan_aset_tetap_before)).'</td>
							<td align="right"><i>Acquisition of Fixed Assets</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan Aset Lain</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_aset_lain_actual < 0 ? "(".number_format(abs($penambahan_aset_lain_actual)).")" : number_format($penambahan_aset_lain_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_aset_lain_before < 0 ? "(".number_format(abs($penambahan_aset_lain_before)).")" : number_format($penambahan_aset_lain_before)).'</td>
							<td align="right"><i>Acquisition of Other Assets</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas investasi</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_investasi_actual < 0 ? "(".number_format(abs($arus_kas_investasi_actual)).")" : number_format($arus_kas_investasi_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_investasi_actual < 0 ? "(".number_format(abs($arus_kas_investasi_actual)).")" : number_format($arus_kas_investasi_actual)).'</td>
							<td align="right"><i>Net cash flow from investment activities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Arus Kas Dari Aktivitas Pendanaan</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><br/><b><i>Cash Flow From Financing Operating</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Simpanan</td>	
							<td align="right">'.($penambahan_simpanan_actual < 0 ? "(".number_format(abs($penambahan_simpanan_actual)).")" : number_format($penambahan_simpanan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_simpanan_before < 0 ? "(".number_format(abs($penambahan_simpanan_before)).")" : number_format($penambahan_simpanan_before)).'</td>
							<td align="right"><i>Increase (Decrease) Savings</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Hibah</td>	
							<td align="right">'.($penambahan_hibah_actual < 0 ? "(".number_format(abs($penambahan_hibah_actual)).")" : number_format($penambahan_hibah_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_hibah_before < 0 ? "(".number_format(abs($penambahan_hibah_before)).")" : number_format($penambahan_hibah_before)).'</td>
							<td align="right"><i>Increase (Decrease) Grants</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Modal Penyertaan</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_modal_actual < 0 ? "(".number_format(abs($penambahan_modal_actual)).")" : number_format($penambahan_modal_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_modal_before < 0 ? "(".number_format(abs($penambahan_modal_before)).")" : number_format($penambahan_modal_before)).'</td>
							<td align="right"><i>Increase (Decrease) Capital Participations</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas pendanaan</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_pendanaan_actual < 0 ? "(".number_format(abs($arus_kas_pendanaan_actual)).")" : number_format($arus_kas_pendanaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_pendanaan_before < 0 ? "(".number_format(abs($arus_kas_pendanaan_before)).")" : number_format($arus_kas_pendanaan_before)).'</td>
							<td align="right"><i>Net cash flow from investment financing</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kenaikan (penurunan) bersih kas dan setara kas</b></td>	
							<td align="right">'.($selisih_kas_actual < 0 ? "(".number_format(abs($selisih_kas_actual)).")" : number_format($selisih_kas_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($selisih_kas_before < 0 ? "(".number_format(abs($selisih_kas_before)).")" : number_format($selisih_kas_before)).'</td>
							<td align="right"><br/><b><i>Net increase (decrease) in cash and cash equivalent</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kas dan setara kas awal tahun</b></td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($kas_awal_tahun_actual < 0 ? "(".number_format(abs($kas_awal_tahun_actual)).")" : number_format($kas_awal_tahun_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($kas_awal_tahun_before < 0 ? "(".number_format(abs($kas_awal_tahun_before)).")" : number_format($kas_awal_tahun_before)).'</td>
							<td align="right"><br/><b><i>Cash and cash equivalent at beginning of year</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kas dan setara kas akhir tahun</b></td>	
							<td align="right" style="border-bottom: 2px solid #000;">'.($kas_akhir_tahun_actual < 0 ? "(".number_format(abs($kas_akhir_tahun_actual)).")" : number_format($kas_akhir_tahun_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($kas_akhir_tahun_before < 0 ? "(".number_format(abs($kas_akhir_tahun_before)).")" : number_format($kas_akhir_tahun_before)).'</td>
							<td align="right"><br/><b><i>Cash and cash equivalent at end of year</i></b></td>
							</tr>';	
				$laporan .= '</table>';
			
			$this->template	->set('menu_title', 'Laporan Arus Kas')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $laporan)
							
							->build('laporan_aruskas');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//DOWNLOAD ARUS KAS
	public function download_aruskas()
	{
		if($this->session->userdata('logged_in'))
		{
			$timestamp=date("Ymdhis");
			$tgl=date("d-M-Y");
			$filename="LAPORAN_ARUS_KAS_$timestamp";	
			$html = "<style> table tr td{ border:0;} </style>";
			$html .= '';
			//$html .= '<h1 align="center">Amartha Microfinance</h1>';
			//$html .= '<hr/>';
			$html .= '<h2 align="center">LAPORAN ARUS KAS</h2><br/>';
			
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				//$date_start =$week_today[0];
				//$date_end = $week_today[1];	
				$date_start = $date_year_today."-01-01";	
				$date_end 	= $date_today;
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
			$arus_kas_operasi_actual = 0;
			$arus_kas_operasi_before = 0;
			
				//Hitung Laba Rugi TODAY
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
							
							//*							
							$grand_total_pendapatan_credit_before += $account_credit;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
							
							//*							
							$grand_total_beban_debet_before += $account_debet;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
					$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
					$grand_total_pendapatan_saldo = $grand_total_pendapatan_before+$grand_total_pendapatan_debet-$grand_total_pendapatan_credit;
					$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
					$laba_rugi = $grand_total_pendapatan_credit - $grand_total_beban_debet;			
					$laba_rugi_before = $grand_total_pendapatan_credit_before - $grand_total_beban_debet_before;			
				//End of Hitung Laba Rugi
			
			//ARUS KAS OPERASI	
				$arus_kas_operasi_actual += $laba_rugi;
				$arus_kas_operasi_before += $laba_rugi_before;
				
				$grand_total_debet=0;
				$grand_total_credit=0;
				$grand_total_before=0;
				
				//Penyusutan aset tetap
				$get_account_saldo = $this->get_account_saldo("1060302",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$actual_1060302 = $total_saldo; 
					$before_1060302 = $total_before;
				$get_account_saldo = $this->get_account_saldo("1060202",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$actual_1060202 = $total_saldo;
					$before_1060202 = $total_before;			
				$penyusutan_aset_tetap_actual = $actual_1060302 + $actual_1060202;
				$penyusutan_aset_tetap_before = $before_1060302 + $before_1060202;
				$arus_kas_operasi_actual += $penyusutan_aset_tetap_actual;
				$arus_kas_operasi_before += $penyusutan_aset_tetap_before;
				
				//Piutang Pembiayaan
				$get_account_saldo = $this->get_account_saldo("1030000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$piutang_pembiayaan_actual = $total_saldo;
					$piutang_pembiayaan_before = $total_before;
					$arus_kas_operasi_actual += $piutang_pembiayaan_actual;
					$arus_kas_operasi_before += $piutang_pembiayaan_before;
				
				//Beban dibayar di muka
				$get_account_saldo = $this->get_account_saldo("1070104",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$beban_dibayar_dimuka_actual = $total_saldo;
					$beban_dibayar_dimuka_before = $total_before;
					$arus_kas_operasi_actual += $beban_dibayar_dimuka_actual;
					$arus_kas_operasi_before += $beban_dibayar_dimuka_before;
					
				//persediaan_barang_cetakan
				$get_account_saldo = $this->get_account_saldo("1070104",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$persediaan_barang_cetakan_actual = $total_saldo;
					$persediaan_barang_cetakan_before = $total_before;
					$arus_kas_operasi_actual += $persediaan_barang_cetakan_actual;
					$arus_kas_operasi_before += $persediaan_barang_cetakan_before;
					
				//simpanan anggota
				$get_account_saldo = $this->get_account_saldo("2010000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_anggota_actual = $total_saldo;
					$simpanan_anggota_before = $total_before;
					$arus_kas_operasi_actual += $simpanan_anggota_actual;
					$arus_kas_operasi_before += $simpanan_anggota_before;
				
				//simpanan berjangka
				$get_account_saldo = $this->get_account_saldo("2020000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_berjangka_actual = $total_saldo;
					$simpanan_berjangka_before = $total_before;
					$arus_kas_operasi_actual += $simpanan_berjangka_actual;
					$arus_kas_operasi_before += $simpanan_berjangka_before;
					
				//hutang pembiayaan
				$get_account_saldo = $this->get_account_saldo("2040000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$hutang_pembiayaan_actual = $total_saldo;
					$hutang_pembiayaan_before = $total_before;
					$arus_kas_operasi_actual += $hutang_pembiayaan_actual;
					$arus_kas_operasi_before += $hutang_pembiayaan_before;
									
				//hutang_lain
				$get_account_saldo = $this->get_account_saldo("2050000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$hutang_lain_actual = $total_saldo;
					$hutang_lain_before = $total_before;
					$arus_kas_operasi_actual += $hutang_lain_actual;
					$arus_kas_operasi_before += $hutang_lain_before;
				
				
			//ARUS KAS INVESTASI
			$arus_kas_investasi_actual = 0;
			$arus_kas_investasi_before = 0;
				
				//Penambahan aset tetap
				$get_account_saldo = $this->get_account_saldo("1060000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_aset_tetap_actual = $total_saldo;
					$penambahan_aset_tetap_before = $total_before;
					$arus_kas_investasi_actual += $penambahan_aset_tetap_actual;
					$arus_kas_investasi_before += $penambahan_aset_tetap_before;
					
				//Penambahan aset lain
				$get_account_saldo = $this->get_account_saldo("1070000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_aset_lain_actual = $total_saldo;
					$penambahan_aset_lain_before = $total_before;
					$arus_kas_investasi_actual += $penambahan_aset_lain_actual;
					$arus_kas_investasi_before += $penambahan_aset_lain_before;
				
			//ARUS KAS PENDANAAN
			$arus_kas_pendanaan_actual = 0;
			$arus_kas_pendanaan_before = 0;
				
				//Penambahan simpanan
				$get_account_saldo = $this->get_account_saldo("3010101",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_simpanan_3010101_actual = $total_saldo;
					$penambahan_simpanan_3010101_before = $total_before;
				$get_account_saldo = $this->get_account_saldo("3010102",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_simpanan_3010102_actual = $total_saldo;
					$penambahan_simpanan_3010102_before = $total_before;
					$penambahan_simpanan_actual = $penambahan_simpanan_3010101_actual + $penambahan_simpanan_3010102_actual;
					$penambahan_simpanan_before = $penambahan_simpanan_3010101_before + $penambahan_simpanan_3010102_before;				
					$arus_kas_pendanaan_actual += $penambahan_simpanan_actual;
					$arus_kas_pendanaan_before += $penambahan_simpanan_before;
				
				//Penambahan hibah			
				$get_account_saldo = $this->get_account_saldo("3010103",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_hibah_actual = $total_saldo;
					$penambahan_hibah_before = $total_before;
					$arus_kas_pendanaan_actual += $penambahan_hibah_actual;
					$arus_kas_pendanaan_before += $penambahan_hibah_before;
					
				//Penambahan modal			
				$get_account_saldo = $this->get_account_saldo("3010202",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$penambahan_modal_actual = $total_saldo;
					$penambahan_modal_before = $total_before;
					$arus_kas_pendanaan_actual += $penambahan_modal_actual;
					$arus_kas_pendanaan_before += $penambahan_modal_before;
					
			//KAS AWAL TAHUN
			$kas_awal_tahun_actual = $arus_kas_operasi_actual + $arus_kas_investasi_actual + $arus_kas_pendanaan_actual;
			$kas_awal_tahun_before = $arus_kas_operasi_before + $arus_kas_investasi_before + $arus_kas_pendanaan_before;
			
			//KAS AKHIR TAHUN		
			$get_account_saldo = $this->get_account_saldo("1010000",1,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
			list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
				$kas_akhir_tahun_actual = $total_saldo;
				$kas_akhir_tahun_before = $total_before;
			
			//SELISIH KAS
			$selisih_kas_actual = $kas_awal_tahun_actual - $kas_akhir_tahun_actual;
			$selisih_kas_before = $kas_awal_tahun_before - $kas_akhir_tahun_before;
			
			//LAPORAN FORMAT
				$laporan .= '<table cellpadding="5px" width="95%" align="center">';
				$laporan .= '<tr>
							<td width="28%" align="left" ></td>	
							<td width="20%" align="center" style="border-bottom: 1px solid #000;">2014</td>							
							<td width="4%" align="left" ></td>	
							<td width="20%" align="center" style="border-bottom: 1px solid #000;">2013</td>
							<td width="28%" align="right"></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><b>Arus Kas Dari Aktivitas Operasi</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><b><i>Cash Flow From Operating Activities</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Laba bersih</td>	
							<td align="right">'.($laba_rugi < 0 ? "(".number_format(abs($laba_rugi)).")" : number_format($laba_rugi)).'</td>							
							<td>&nbsp;</td>
							<td align="right">'.($laba_rugi_before < 0 ? "(".number_format(abs($laba_rugi_before)).")" : number_format($laba_rugi_before)).'</td>
							<td align="right"><i>Net Income</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyesuaian untuk merekonsiliasi Laba bersih menjadi arus kas bersih dari aktivitas operasi:</td>	
							<td align="right"> </td>
							<td>&nbsp;</td>
							<td align="right"> </td>
							<td align="right"><i>Adjustment to reconcile net income to be net cash from Operationg activities:</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyusutan aset tetap</td>	
							<td align="right">'.($penyusutan_aset_tetap_actual < 0 ? "(".number_format(abs($penyusutan_aset_tetap_actual)).")" : number_format($penyusutan_aset_tetap_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penyusutan_aset_tetap_before < 0 ? "(".number_format(abs($penyusutan_aset_tetap_before)).")" : number_format($penyusutan_aset_tetap_before)).'</td>
							<td align="right"><i>Depreciation of fixed assets</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penyusutan Aset dan Liabilitas dari Operasional: </td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><i>Changes in operating assets and liablities</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Piutang Pembiayaan</td>	
							<td align="right">'.($piutang_pembiayaan_actual < 0 ? "(".number_format(abs($piutang_pembiayaan_actual)).")" : number_format($piutang_pembiayaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($piutang_pembiayaan_before < 0 ? "(".number_format(abs($piutang_pembiayaan_before)).")" : number_format($piutang_pembiayaan_before)).'</td>
							<td align="right"><i>Financing Receivables</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Beban dibayar dimuka</td>	
							<td align="right">'.($beban_dibayar_dimuka_actual < 0 ? "(".number_format(abs($beban_dibayar_dimuka_actual)).")" : number_format($beban_dibayar_dimuka_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($beban_dibayar_dimuka_before < 0 ? "(".number_format(abs($beban_dibayar_dimuka_before)).")" : number_format($beban_dibayar_dimuka_before)).'</td>
							<td align="right"><i>Prepaid Expenses</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Persediaan Barang Cetakan</td>	
							<td align="right">'.($persediaan_barang_cetakan_actual < 0 ? "(".number_format(abs($persediaan_barang_cetakan_actual)).")" : number_format($persediaan_barang_cetakan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($persediaan_barang_cetakan_before < 0 ? "(".number_format(abs($persediaan_barang_cetakan_before)).")" : number_format($persediaan_barang_cetakan_before)).'</td>
							<td align="right"><i>Inventory</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Simpanan Anggota</td>	
							<td align="right">'.($simpanan_anggota_actual < 0 ? "(".number_format(abs($simpanan_anggota_actual)).")" : number_format($simpanan_anggota_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($simpanan_anggota_before < 0 ? "(".number_format(abs($simpanan_anggota_before)).")" : number_format($simpanan_anggota_before)).'</td>
							<td align="right"><i>Members Savings</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Simpanan Berjangka</td>	
							<td align="right">'.($simpanan_berjangka_actual < 0 ? "(".number_format(abs($simpanan_berjangka_actual)).")" : number_format($simpanan_berjangka_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($simpanan_berjangka_before < 0 ? "(".number_format(abs($simpanan_berjangka_before)).")" : number_format($simpanan_berjangka_before)).'</td>
							<td align="right"><i>Term Deposits</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Hutang Pembiayaan</td>	
							<td align="right">'.($hutang_pembiayaan_actual < 0 ? "(".number_format(abs($hutang_pembiayaan_actual)).")" : number_format($hutang_pembiayaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($hutang_pembiayaan_before < 0 ? "(".number_format(abs($hutang_pembiayaan_before)).")" : number_format($hutang_pembiayaan_before)).'</td>
							<td align="right"><i>Financing Payables</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Hutang Lain-lain</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($hutang_lain_actual < 0 ? "(".number_format(abs($hutang_lain_actual)).")" : number_format($hutang_lain_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($hutang_lain_before < 0 ? "(".number_format(abs($hutang_lain_before)).")" : number_format($hutang_lain_before)).'</td>
							<td align="right"><i>Other Liabilities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas operasi</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_operasi_actual < 0 ? "(".number_format(abs($arus_kas_operasi_actual)).")" : number_format($arus_kas_operasi_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_operasi_before < 0 ? "(".number_format(abs($arus_kas_operasi_before)).")" : number_format($arus_kas_operasi_before)).'</td>
							<td align="right"><i>Net cash flow from operating activities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Arus Kas Dari Aktivitas Investasi</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><br/><b><i>Cash Flow From Investment Operating</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan Aset Tetap</td>	
							<td align="right">'.($penambahan_aset_tetap_actual < 0 ? "(".number_format(abs($penambahan_aset_tetap_actual)).")" : number_format($penambahan_aset_tetap_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_aset_tetap_before < 0 ? "(".number_format(abs($penambahan_aset_tetap_before)).")" : number_format($penambahan_aset_tetap_before)).'</td>
							<td align="right"><i>Acquisition of Fixed Assets</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan Aset Lain</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_aset_lain_actual < 0 ? "(".number_format(abs($penambahan_aset_lain_actual)).")" : number_format($penambahan_aset_lain_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_aset_lain_before < 0 ? "(".number_format(abs($penambahan_aset_lain_before)).")" : number_format($penambahan_aset_lain_before)).'</td>
							<td align="right"><i>Acquisition of Other Assets</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas investasi</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_investasi_actual < 0 ? "(".number_format(abs($arus_kas_investasi_actual)).")" : number_format($arus_kas_investasi_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_investasi_actual < 0 ? "(".number_format(abs($arus_kas_investasi_actual)).")" : number_format($arus_kas_investasi_actual)).'</td>
							<td align="right"><i>Net cash flow from investment activities</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Arus Kas Dari Aktivitas Pendanaan</b></td>	
							<td align="right"></td>
							<td>&nbsp;</td>
							<td align="right"></td>
							<td align="right"><br/><b><i>Cash Flow From Financing Operating</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Simpanan</td>	
							<td align="right">'.($penambahan_simpanan_actual < 0 ? "(".number_format(abs($penambahan_simpanan_actual)).")" : number_format($penambahan_simpanan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_simpanan_before < 0 ? "(".number_format(abs($penambahan_simpanan_before)).")" : number_format($penambahan_simpanan_before)).'</td>
							<td align="right"><i>Increase (Decrease) Savings</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Hibah</td>	
							<td align="right">'.($penambahan_hibah_actual < 0 ? "(".number_format(abs($penambahan_hibah_actual)).")" : number_format($penambahan_hibah_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($penambahan_hibah_before < 0 ? "(".number_format(abs($penambahan_hibah_before)).")" : number_format($penambahan_hibah_before)).'</td>
							<td align="right"><i>Increase (Decrease) Grants</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan (Pengurangan) Modal Penyertaan</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_modal_actual < 0 ? "(".number_format(abs($penambahan_modal_actual)).")" : number_format($penambahan_modal_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($penambahan_modal_before < 0 ? "(".number_format(abs($penambahan_modal_before)).")" : number_format($penambahan_modal_before)).'</td>
							<td align="right"><i>Increase (Decrease) Capital Participations</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Arus kas bersih dari aktivitas pendanaan</td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_pendanaan_actual < 0 ? "(".number_format(abs($arus_kas_pendanaan_actual)).")" : number_format($arus_kas_pendanaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($arus_kas_pendanaan_before < 0 ? "(".number_format(abs($arus_kas_pendanaan_before)).")" : number_format($arus_kas_pendanaan_before)).'</td>
							<td align="right"><i>Net cash flow from investment financing</i></td>
							</tr>';
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kenaikan (penurunan) bersih kas dan setara kas</b></td>	
							<td align="right">'.($selisih_kas_actual < 0 ? "(".number_format(abs($selisih_kas_actual)).")" : number_format($selisih_kas_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right">'.($selisih_kas_before < 0 ? "(".number_format(abs($selisih_kas_before)).")" : number_format($selisih_kas_before)).'</td>
							<td align="right"><br/><b><i>Net increase (decrease) in cash and cash equivalent</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kas dan setara kas awal tahun</b></td>	
							<td align="right" style="border-bottom: 1px solid #000;">'.($kas_awal_tahun_actual < 0 ? "(".number_format(abs($kas_awal_tahun_actual)).")" : number_format($kas_awal_tahun_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">'.($kas_awal_tahun_before < 0 ? "(".number_format(abs($kas_awal_tahun_before)).")" : number_format($kas_awal_tahun_before)).'</td>
							<td align="right"><br/><b><i>Cash and cash equivalent at beginning of year</i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><br/><b>Kas dan setara kas akhir tahun</b></td>	
							<td align="right" style="border-bottom: 2px solid #000;">'.($kas_akhir_tahun_actual < 0 ? "(".number_format(abs($kas_akhir_tahun_actual)).")" : number_format($kas_akhir_tahun_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($kas_akhir_tahun_before < 0 ? "(".number_format(abs($kas_akhir_tahun_before)).")" : number_format($kas_akhir_tahun_before)).'</td>
							<td align="right"><br/><b><i>Cash and cash equivalent at end of year</i></b></td>
							</tr>';	
				$laporan .= '</table>';
			
			$html .= $laporan;
			$this->load->library('mpdf');
			$mpdf=new mPDF('utf-8', 'A4-L');
			$mpdf->SetHeader("Amartha Microfinance".'||'.$tgl.'|'); 
			$mpdf->SetFooter("Laporan Arus Kas".'||{PAGENO}|'); 
			$mpdf->WriteHTML($html);
			//echo $html;
			//$this->mpdf->Output();
			$pdfFilePath = FCPATH."downloads/aruskas/$filename.pdf";
			$pdffile = base_url()."downloads/aruskas/$filename.pdf";
			$mpdf->Output($pdfFilePath,'F');
			
			redirect($pdffile, 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//LAPORAN EKUITAS
	public function laporan_ekuitas()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_end);
			$date_end_before = strtotime("-7 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
				//Simpanan WAJIB
				$get_account_saldo = $this->get_account_saldo("3010101",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_wajib_actual = $total_saldo;
					$simpanan_wajib_before = $total_before;
					
				//Simpanan POKOK
				$get_account_saldo = $this->get_account_saldo("3010102",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$simpanan_pokok_actual = $total_saldo;
					$simpanan_pokok_before = $total_before;
				
				//Simpanan TOTAL
					$simpanan_actual = $simpanan_wajib_actual + $simpanan_pokok_actual;
					$simpanan_before = $simpanan_wajib_before + $simpanan_pokok_before;
			
				//Hibah
				$get_account_saldo = $this->get_account_saldo("3010103",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$hibah_actual = $total_saldo;
					$hibah_before = $total_before;					
					
				//Modal Penyertaan
				$get_account_saldo = $this->get_account_saldo("3010202",3,$date_start, $date_end, $date_start_before, $date_end_before, $user_branch);
				list($total_before, $total_debet, $total_credit, $total_saldo) = $get_account_saldo;			
					$modal_penyertaan_actual = $total_saldo;
					$modal_penyertaan_before = $total_before;
				
				//SHU
				$shu_actual = 0;
				$shu_before = 0;				
				
				//TOTAL EKUITAS
					$total_ekuitas_actual = $simpanan_actual + $hibah_actual + $modal_penyertaan_actual + $shu_actual;
					$total_ekuitas_before = $simpanan_before + $hibah_before + $modal_penyertaan_before + $shu_before;
					
					
				//LAPORAN
				$laporan .= '<table cellpadding="5px" cellspacing="10" width="95%" align="center">';
				$laporan .= '<tr>
							<td width="20%" align="left" ></td>	
							<td width="10%" align="center" style="border-bottom: 1px solid #000;">Simpanan</td>	
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;">Hibah</td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;">Modal Penyertaan</td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;">SHU</td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;">Jumlah Ekuitas</td>
							<td width="20%" align="right"></td>
							</tr>';	
				$laporan .= '<tr>
							<td width="20%" align="left" ></td>	
							<td width="10%" align="center" style="border-bottom: 1px solid #000;"><i>Savings</i></td>	
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;"><i>Grants</i></td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;"><i>Capital Participations</i></td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;"><i>Operating Result</i></td>
							<td>&nbsp;</td>
							<td width="10%" align="center" style="border-bottom: 1px solid #000;"><i>Total Equity</i></td>
							<td width="20%" align="right"></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><b>Saldo 31 Desember 2010</b></td>	
							<td align="right"><b>14.000.000</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>-</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>171.500.000</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>(12.188.761)</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>172311239</b></td>
							<td align="right"><b><i>Balance as at December 31, 2010<i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan</td>	
							<td align="right">-</td>
							<td>&nbsp;</td>
							<td align="right">50.000.000</td>
							<td>&nbsp;</td>
							<td align="right">172.095.000</td>
							<td>&nbsp;</td>
							<td align="right">-</td>
							<td>&nbsp;</td>
							<td align="right">222.095.000</td>
							<td align="right"><i>Aditional</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Sisa Hasil Usaha</td>	
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">(89.200.754)</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">(89.200.754)</td>
							<td align="right"><i>Operating Result</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><b>Saldo 31 Desember 2011</b></td>	
							<td align="right"><b>14.000.000</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>50.000.000</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>343.595.000</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>(101.389.515)</b></td>
							<td>&nbsp;</td>
							<td align="right"><b>306.205.485</b></td>
							<td align="right"><b><i>Balance as at December 31, 2011<i></b></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Penambahan</td>	
							<td align="right">-</td>
							<td>&nbsp;</td>
							<td align="right">104.730.000</td>
							<td>&nbsp;</td>
							<td align="right">80.609.546</td>
							<td>&nbsp;</td>
							<td align="right">-</td>
							<td>&nbsp;</td>
							<td align="right">185.339.546</td>
							<td align="right"><i>Aditional</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" >Sisa Hasil Usaha</td>	
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">-</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">25.296.367</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 1px solid #000;">25.296.367</td>
							<td align="right"><i>Operating Result</i></td>
							</tr>';	
				$laporan .= '<tr>
							<td align="left" ><b>Saldo 31 Desember 2012</b></td>	
							<td align="right" style="border-bottom: 2px solid #000;">'.($simpanan_actual < 0 ? "(".number_format(abs($simpanan_actual)).")" : number_format($simpanan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($hibah_actual < 0 ? "(".number_format(abs($hibah_actual)).")" : number_format($hibah_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($modal_penyertaan_actual < 0 ? "(".number_format(abs($modal_penyertaan_actual)).")" : number_format($modal_penyertaan_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($shu_actual < 0 ? "(".number_format(abs($shu_actual)).")" : number_format($shu_actual)).'</td>
							<td>&nbsp;</td>
							<td align="right" style="border-bottom: 2px solid #000;">'.($total_ekuitas_actual < 0 ? "(".number_format(abs($total_ekuitas_actual)).")" : number_format($total_ekuitas_actual)).'</td>
							<td align="right"><b><i>Balance as at December 31, 2012<i></b></td>
							</tr>';	
				$laporan .= '</table><br/>';
			
			$this->template	->set('menu_title', 'Laporan Ekuitas')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $laporan)
							->build('laporan');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	public function jurnal_excel()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			$branch_name = str_replace(' ', '', $this->session->userdata('user_branch_name'));
			
			$date_start = $this->uri->segment(3);
			$date_end = $this->uri->segment(4);
			$key = $this->uri->segment(5);
			$q = $this->uri->segment(6);
			//echo $date_start."---".$date_end;
			//Get Jurnal
			$total_rows = $this->jurnal_model->count_all_jurnal($q, $key,$user_branch,$date_start,$date_end);
			
			$jurnal = $this->jurnal_model->get_all_jurnal($config['per_page'] , $page, $q, $key, $user_branch,$date_start,$date_end);
			
			//load our new PHPExcel library
			$this->load->library('excel');
		 
			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()->setCreator("Amartha MIS");
			$objPHPExcel->getProperties()->setLastModifiedBy("Amartha MIS");
			$objPHPExcel->getProperties()->setTitle("Jurnal");
			$objPHPExcel->getProperties()->setSubject("Jurnal");
			$objPHPExcel->getProperties()->setDescription("Jurnal");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('Jurnal');
			
			//TITLE
			$objPHPExcel->getActiveSheet()->setCellValue("A1", "Amartha Microfinance");
			$objPHPExcel->getActiveSheet()->setCellValue("A2", "Cabang $branch_name");
			$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
			$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));
			$objPHPExcel->getActiveSheet()->getStyle("A2")->applyFromArray(array("font" => array( "bold" => true)));
			//TOP ROW
			$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->applyFromArray(array("font" => array( "bold" => true)));			
			$objPHPExcel->getActiveSheet()->setCellValue("A4", "TANGGAL");
			$objPHPExcel->getActiveSheet()->setCellValue("B4", "FLAG");
			$objPHPExcel->getActiveSheet()->setCellValue("C4", "AKUN");
			$objPHPExcel->getActiveSheet()->setCellValue("D4", "NAMA AKUN");
			$objPHPExcel->getActiveSheet()->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->setCellValue("E4", "KETERANGAN");
			$objPHPExcel->getActiveSheet()->setCellValue("F4", "JUMLAH");
			$objPHPExcel->getActiveSheet()->setCellValue("G4", "NO BUKTI");
			$objPHPExcel->getActiveSheet()->getStyle("G4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells("G4:I4");
				$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(4);
				$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(4);
				$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(4);
			//$objPHPExcel->getActiveSheet()->setCellValue("F4", " ");
			
			$no=5;
			foreach($jurnal as $c){
				$no_a = $no;
				$no_b = $no+1;
				$jurnal_date = $c->jurnal_date;
				if($c->jurnal_nobukti_kode != "-" OR $c->jurnal_nobukti_nomor != "-"){
					$jurnal_month = substr($jurnal_date, 5, 2); 
				}else{
					$jurnal_month = "-";
				}
				
				$jurnal_nobukti_kode = $c->jurnal_nobukti_kode;
				$jurnal_nobukti_nomor = $c->jurnal_nobukti_nomor;
				
				$objPHPExcel->getActiveSheet()->setCellValue("A$no_a", "$c->jurnal_date");
				$objPHPExcel->getActiveSheet()->setCellValue("A$no_b", "$c->jurnal_date");
				$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
				
				$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(3);
				$objPHPExcel->getActiveSheet()->setCellValue("B$no_a", "D");				
				$objPHPExcel->getActiveSheet()->setCellValue("B$no_b", "K");				
				$objPHPExcel->getActiveSheet()->getStyle("B$no_a:B$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$objPHPExcel->getActiveSheet()->setCellValue("C$no_a", "$c->accounting_debet_code");
				$objPHPExcel->getActiveSheet()->setCellValue("C$no_b", "$c->accounting_credit_code");
				$objPHPExcel->getActiveSheet()->getStyle("C$no_a:C$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$objPHPExcel->getActiveSheet()->setCellValue("D$no_a", "$c->accounting_debet_name");
				$objPHPExcel->getActiveSheet()->setCellValue("D$no_b", "$c->accounting_credit_name");
				$objPHPExcel->getActiveSheet()->getStyle("D$no_a:D$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(60);
				
				$objPHPExcel->getActiveSheet()->mergeCells("E$no_a:E$no_b");
				$objPHPExcel->getActiveSheet()->setCellValue("E$no_a", "$c->jurnal_remark");				
				$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(60);
				
				//$objPHPExcel->getActiveSheet()->mergeCells("F$no_a:F$no_b");
				$objPHPExcel->getActiveSheet()->setCellValue("F$no_a", $c->jurnal_debet);
				$objPHPExcel->getActiveSheet()->setCellValue("F$no_b", $c->jurnal_credit);
				$objPHPExcel->getActiveSheet()->getStyle("F$no_a:F$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				
				$objPHPExcel->getActiveSheet()->setCellValue("G$no_a", $c->jurnal_nobukti_kode);
				$objPHPExcel->getActiveSheet()->setCellValue("G$no_b", $c->jurnal_nobukti_kode);				
				$objPHPExcel->getActiveSheet()->setCellValue("H$no_a", $jurnal_month);				
				$objPHPExcel->getActiveSheet()->setCellValue("H$no_b", $jurnal_month);			
				$objPHPExcel->getActiveSheet()->getCell("I$no_a")->setValueExplicit("$c->jurnal_nobukti_nomor", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getCell("I$no_b")->setValueExplicit("$c->jurnal_nobukti_nomor", PHPExcel_Cell_DataType::TYPE_STRING);	
							
				$objPHPExcel->getActiveSheet()->getStyle("G$no_a:G$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle("H$no_a:H$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
				$objPHPExcel->getActiveSheet()->getStyle("I$no_a:I$no_b")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$no = $no+2; 
			}
			
			//EXPORT	
			$filename = "Jurnal_".$branch_name."_" . time() . '.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			
			//redirect('accounting/jurnal', 'refresh');
					 
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	//NERACA EXCEL
	public function neraca_excel()
	{
		if($this->session->userdata('logged_in'))
		{
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			$branch_name = str_replace(' ', '', $this->session->userdata('user_branch_name'));
			
			//if($user_branch == "0"){ $user_branch=NULL;}
			
			//load our new PHPExcel library
			$this->load->library('excel');
		 
			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()->setCreator("Amartha MIS");
			$objPHPExcel->getProperties()->setLastModifiedBy("Amartha MIS");
			$objPHPExcel->getProperties()->setTitle("Neraca");
			$objPHPExcel->getProperties()->setSubject("Neraca");
			$objPHPExcel->getProperties()->setDescription("Neraca");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('Neraca');
			
			//TITLE
			$objPHPExcel->getActiveSheet()->setCellValue("A1", "Amartha Microfinance");
			$objPHPExcel->getActiveSheet()->setCellValue("A2", "Cabang $branch_name");
			$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
			$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));
			$objPHPExcel->getActiveSheet()->getStyle("A2")->applyFromArray(array("font" => array( "bold" => true)));
			//TOP ROW
			$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->applyFromArray(array("font" => array( "bold" => true)));
			$objPHPExcel->getActiveSheet()->getStyle("B4:E4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);			
			$objPHPExcel->getActiveSheet()->setCellValue("A4", "ACCOUNT");
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(60);
			$objPHPExcel->getActiveSheet()->setCellValue("B4", "SALDO AWAL");
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
			$objPHPExcel->getActiveSheet()->setCellValue("C4", "DEBET");
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
			$objPHPExcel->getActiveSheet()->setCellValue("D4", "CREDIT");
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
			$objPHPExcel->getActiveSheet()->setCellValue("E4", "SALDO AKHIR");
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
			
			
			$timestamp=date("Ymdhis");
			
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			$date_start=$this->uri->segment(3);
			$date_end=$this->uri->segment(4);
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->uri->segment(3);
				$date_end=$this->uri->segment(4);
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_start);
			//$date_end_before = $date_start;
			$date_end_before = strtotime("-1 day", $date_end_before); 
			$date_end_before = date('Y-m-d', $date_end_before);			
			$date_start_before = "2013-01-01";
			
				
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						//$account_saldo_before = $account_debet_before - $account_credit_before;
						//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						if($code_level0 == "4"){					
							$account_saldo_before = $account_credit_before -  $account_debet_before;
							$account_saldo = $account_saldo_before + $account_credit - $account_debet;
						}elseif($code_level0 == "5"){
							$account_saldo_before = $account_debet_before - $account_credit_before;
							$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
						}
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$grand_total_pendapatan_saldo = $grand_total_pendapatan_before-$grand_total_pendapatan_debet+$grand_total_pendapatan_credit;
				$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
				//$laba_rugi = ($grand_total_pendapatan_before+$grand_total_pendapatan_credit) - ($grand_total_beban_before+$grand_total_beban_debet);
				$laba_rugi = $grand_total_pendapatan_saldo - $grand_total_beban_saldo;
				$laba_rugi_before = $grand_total_pendapatan_before - $grand_total_beban_before;				
				//End of Hitung Laba Rugi
			
			
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			
			//ASET			
			$objPHPExcel->getActiveSheet()->setCellValue("A5", "ASET");
			$objPHPExcel->getActiveSheet()->getStyle("A5")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));			
			$accounting = $this->accounting_model->get_all_accounting_aset()->result();
			$no=6;
			foreach($accounting as $c){
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ 
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "");
				}
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", $c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", $account_saldo_before);
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", $account_debet);
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", $account_credit);
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", $account_saldo);					
					$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "  ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", $account_saldo_before);
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", $account_debet);
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", $account_credit);
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", $account_saldo);
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$branch);
					
					$account_saldo_before = $account_debet_before - $account_credit_before;
					$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
						$grand_total_aktiva_debet += $account_debet;
						$grand_total_aktiva_credit += $account_credit;
						$grand_total_aktiva_before += $account_saldo_before;					
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "    ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)));
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($account_debet));
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($account_credit));
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							
				}	
				$code_level0_old = $code_level0;
				$no++;
			}
			
			
			$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit;				
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "TOTAL ASET");
			$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($grand_total_aktiva_before < 0 ? "(".number_format(abs($grand_total_aktiva_before)).")" : number_format($grand_total_aktiva_before)));
			$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($grand_total_aktiva_debet));
			$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($grand_total_aktiva_credit));
			$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($grand_total_aktiva_saldo < 0 ? "(".number_format(abs($grand_total_aktiva_saldo)).")" : number_format($grand_total_aktiva_saldo)));
			$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
			$no++;	
			
			//reset
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			$grand_total_aktiva_before = 0;
			$grand_total_aktiva_debet = 0;
			$grand_total_aktiva_credit = 0;
			$grand_total_aktiva_saldo = 0;
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "");
			$no++;	
			
			//KEWAJIBAN			
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "KEWAJIBAN");
			$objPHPExcel->getActiveSheet()->getStyle("A$no")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));	
			$no++;	
			$accounting = $this->accounting_model->get_all_accounting_kewajiban()->result();
			foreach($accounting as $c){
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ 
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "");
				}
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", $c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)));
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($account_debet));
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($account_credit));
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)));					
					$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "  ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)));
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($account_debet));
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($account_credit));
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$branch);
					
					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					if($code_level0 == "2"){					
						$account_saldo_before = $account_credit_before - $account_debet_before;
						$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					}else{
						$account_saldo_before = $account_debet_before - $account_credit_before;
						$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
					}
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					
						$grand_total_aktiva_debet += $account_debet;
						$grand_total_aktiva_credit += $account_credit;
						$grand_total_aktiva_before += $account_saldo_before;					
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "    ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)));
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($account_debet));
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($account_credit));
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							
				}	
				$code_level0_old = $code_level0;
				$no++;
			}
			
			
			$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit;				
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "TOTAL KEWAJIBAN"); 
			$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($grand_total_aktiva_before < 0 ? "(".number_format(abs($grand_total_aktiva_before)).")" : number_format($grand_total_aktiva_before)));
			$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($grand_total_aktiva_debet));
			$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($grand_total_aktiva_credit));
			$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($grand_total_aktiva_saldo < 0 ? "(".number_format(abs($grand_total_aktiva_saldo)).")" : number_format($grand_total_aktiva_saldo)));
			$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
			$no++;	
			
			
			//reset
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			$grand_total_aktiva_before = 0;
			$grand_total_aktiva_debet = 0;
			$grand_total_aktiva_credit = 0;
			$grand_total_aktiva_saldo = 0;
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "");
			$no++;	
			
			//MODAL	
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "MODAL");
			$objPHPExcel->getActiveSheet()->getStyle("A$no")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 16)));	
			$no++;	
			$accounting = $this->accounting_model->get_all_accounting_modal()->result();
			foreach($accounting as $c){
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ 
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "");
				}
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$account_saldo_before = $account_credit_before - $account_debet_before;					
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", $c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", $account_saldo_before);
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", $account_debet);
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", $account_credit);
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", $account_saldo);					
					$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						
					
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$branch);
					
					//$account_saldo_before = $account_debet_before - $account_credit_before;
					//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
					
					$account_saldo_before = $account_credit_before - $account_debet_before;					
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "  ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", $account_saldo_before);
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", $account_debet);
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", $account_credit);
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", $account_saldo);
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;
					
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi_before;
						$account_saldo_before = 0;
						$account_credit = $laba_rugi;
					}elseif($c->accounting_code == "3020001"){
						$account_saldo_before = $account_debet_before - $account_credit_before ;
					}
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					  
						$grand_total_modal_debet += $account_debet;
						$grand_total_modal_credit += $account_credit;
						$grand_total_modal_before += $account_saldo_before;		
					
					$objPHPExcel->getActiveSheet()->setCellValue("A$no", "    ".$c->accounting_code." ".$c->accounting_name);
					$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)));
					$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($account_debet));
					$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($account_credit));
					$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)));
					$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							
				}	
				$code_level0_old = $code_level0;
				$no++;
			}
			
			
			$grand_total_aktiva_saldo = $grand_total_aktiva_before+$grand_total_aktiva_debet-$grand_total_aktiva_credit;				
			$objPHPExcel->getActiveSheet()->setCellValue("A$no", "TOTAL MODAL"); 
			$objPHPExcel->getActiveSheet()->setCellValue("B$no", ($grand_total_aktiva_before < 0 ? "(".number_format(abs($grand_total_aktiva_before)).")" : number_format($grand_total_aktiva_before)));
			$objPHPExcel->getActiveSheet()->setCellValue("C$no", number_format($grand_total_aktiva_debet));
			$objPHPExcel->getActiveSheet()->setCellValue("D$no", number_format($grand_total_aktiva_credit));
			$objPHPExcel->getActiveSheet()->setCellValue("E$no", ($grand_total_aktiva_saldo < 0 ? "(".number_format(abs($grand_total_aktiva_saldo)).")" : number_format($grand_total_aktiva_saldo)));
			$objPHPExcel->getActiveSheet()->getStyle("B$no:E$no")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle("A$no:E$no")->applyFromArray(array("font" => array( "bold" => true)));
			$no++;	
			
			
			//EXPORT	
			$filename = "Neraca_".$branch_name."_" . time() . '.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			
			redirect('accounting/neraca', 'refresh');
		}
		else
		{
		  //If no session, redirect to login page
		  redirect('login', 'refresh');
		}
	}
	
	
	//NERACA
	public function neraca_test()
	{
		
			//Cek User Branch
			$user_branch = $this->session->userdata('user_branch');
			
			////if($user_branch == "0"){ $user_branch=NULL;}
			
			function getStartAndEndDate($week, $year)
			{
				$time = strtotime("1 January $year", time());
				$day = date('w', $time);
				$time += ((7*$week)+1-$day)*24*3600;
				$return[0] = date('Y-n-j', $time);
				$time += 6*24*3600;
				$return[1] = date('Y-n-j', $time);
				return $return;
			}
			
			$date_today=date("Y-m-d");
			$date_year_today=date("Y");
			$date_week_today=date("W", strtotime($date_today)) - 1;
			$date_week_before=$date_week_today-1;
			
			$week_today = getStartAndEndDate($date_week_today,$date_year_today);
			
			$date_start=$this->input->post('date_start');
			$date_end=$this->input->post('date_end');
			
			if($date_start AND $date_end AND ($date_start <= $date_end )){
				$date_start=$this->input->post('date_start');
				$date_end=$this->input->post('date_end');
			}else{
				$date_start =$week_today[0];
				$date_end = $week_today[1];			
			}
			
			$date_end_before = strtotime($date_start);
			$date_end_before = strtotime("-1 day", $date_end_before);
			$date_end_before = date('Y-m-d', $date_end_before);	  		
			$date_start_before = "2013-01-01"; 
			
				//Hitung Laba Rugi
				$accounting = $this->accounting_model->get_all_accounting_labarugi()->result();
				foreach($accounting as $c):
					$code = $c->accounting_code;
					$code_level0 = substr($code,0,1);
					$code_level1 = substr($code,0,3);
					$code_level2 = substr($code,0,5);
					$haschild = $c->accounting_haschild;
					$parent = $c->accounting_parent;
					$account_debet = 0;
					$account_credit = 0;
					$account_saldo = 0;
					$saldo_awal =0;
					
					if($haschild == "1" AND $parent == "0"){
					}elseif($haschild == "1" AND $parent != "0"){
					}else{	
						//LEVEL 3					
						$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
						$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);
						
						$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
						$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
						
						//$account_saldo_before = $account_debet_before - $account_credit_before;
						//$account_saldo = $account_saldo_before + $account_debet - $account_credit;
						
						if($code_level0 == "4"){					
							$account_saldo_before = $account_credit_before -  $account_debet_before;
							$account_saldo = $account_saldo_before + $account_credit - $account_debet;
						}elseif($code_level0 == "5"){
							$account_saldo_before = $account_debet_before - $account_credit_before;
							$account_saldo = $account_saldo_before + $account_debet - $account_credit;					
						}
						
						//grand total dihitung dari total account level 3
						$grand_total_debet += $account_debet;
						$grand_total_credit += $account_credit;
						$grand_total_before += $account_saldo_before;
						
						if($code_level0 == "4"){
							$grand_total_pendapatan_debet += $account_debet;
							$grand_total_pendapatan_credit += $account_credit;
							$grand_total_pendapatan_before += $account_saldo_before;
						}elseif($code_level0 == "5"){
							$grand_total_beban_debet += $account_debet;
							$grand_total_beban_credit += $account_credit;
							$grand_total_beban_before += $account_saldo_before;
						}
					}	
					$code_level0_old = $code_level0;
				endforeach; 
				
				//GRAND TOTAL LABA RUGI BERJALAN			
				//GRAND TOTAL				
				$grand_total_saldo = $saldo_awal+$grand_total_debet-$grand_total_credit;
				$grand_total_pendapatan_saldo = $grand_total_pendapatan_before-$grand_total_pendapatan_debet+$grand_total_pendapatan_credit;
				$grand_total_beban_saldo = $grand_total_beban_before+$grand_total_beban_debet-$grand_total_beban_credit;
				//$laba_rugi = ($grand_total_pendapatan_before+$grand_total_pendapatan_credit) - ($grand_total_beban_before+$grand_total_beban_debet);
				$laba_rugi = $grand_total_pendapatan_saldo - $grand_total_beban_saldo;
				$laba_rugi_before = $grand_total_pendapatan_before - $grand_total_beban_before;				
				//End of Hitung Laba Rugi
			
			
			$grand_total_debet=0;
			$grand_total_credit=0;
			$grand_total_before=0;
			
			//ASET
			$accounting = $this->accounting_model->get_all_accounting_aset()->result();
			$get_neraca = $this->print_neraca("ASET",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			$grand_total_aktiva_saldo_before = $aset_before;
			$grand_total_aktiva_saldo = $aset_saldo;
			
			//KEWAJIBAN
			$accounting = $this->accounting_model->get_all_accounting_kewajiban()->result();
			$get_neraca = $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before,$user_branch);
			list($neraca_list, $aset_before, $aset_debet, $aset_credit, $aset_saldo) = $get_neraca;			
			$neraca .= $neraca_list;
			//$neraca .= $this->print_neraca("KEWAJIBAN",$accounting,$date_start,$date_end,$date_start_before,$date_end_before);
			
			$grand_total_kewajiban_saldo_before = $aset_before;
			$grand_total_kewajiban_saldo = $aset_saldo;
				
			//MODAL
			$accounting = $this->accounting_model->get_all_accounting_modal()->result();
			foreach($accounting as $c):
				$code = $c->accounting_code;
				$code_level0 = substr($code,0,1);
				$code_level1 = substr($code,0,3);
				$code_level2 = substr($code,0,5);
				$haschild = $c->accounting_haschild;
				$parent = $c->accounting_parent;
				$account_debet = 0;
				$account_credit = 0;
				$account_saldo = 0;
				$saldo_awal =0;
				if($code_level0_old != $code_level0){ $neraca .= '<tr><td colspan="5">&nbsp;</td></tr><tr>'; }
				if($haschild == "1" AND $parent == "0"){
					//LEVEL 1						
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level1,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;				
					$account_saldo = $account_saldo_before - $account_debet + $account_credit + $laba_rugi;
					
					/*$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>								
								</tr>';*/
					$neraca .= '<tr>     
								<td align="left" ><b>'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td>
								<td class="text-right"><b></b></td> 								
								</tr>';
				}elseif($haschild == "1" AND $parent != "0"){
					//LEVEL 2					
					$account_debet = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_parent_debet_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_parent_credit_by_date($code_level2,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before ;
					
					$account_saldo = $account_saldo_before - $account_debet + $account_credit;
					
					$neraca .= '<tr>     
								<td align="left" ><b>&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</b></td>	
								<td class="text-right"><b>'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</b></td>
								<td class="text-right"><b>'.number_format($account_debet).'</b></td>
								<td class="text-right"><b>'.number_format($account_credit).'</b></td>
								<td class="text-right"><b>'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</b></td>
								</tr>';
				}else{	
					//LEVEL 3					
					$account_debet = $this->jurnal_model->sum_account_debet_by_date($code,$date_start,$date_end,$user_branch);
					$account_credit = $this->jurnal_model->sum_account_credit_by_date($code,$date_start,$date_end,$user_branch);

					$account_debet_before = $this->jurnal_model->sum_account_debet_by_date($code,$date_start_before,$date_end_before,$user_branch);
					$account_credit_before = $this->jurnal_model->sum_account_credit_by_date($code,$date_start_before,$date_end_before,$user_branch);
					
					$account_saldo_before = $account_credit_before - $account_debet_before;
					
					if($c->accounting_code == "3020002"){
						$account_saldo_before = $laba_rugi_before;
						$account_saldo_before = 0;
						$account_credit = $laba_rugi;
					}elseif($c->accounting_code == "3020001"){
						$account_saldo_before = $account_debet_before - $account_credit_before ;
					}
					$account_saldo = $account_saldo_before + $account_credit - $account_debet;
					//grand total dihitung dari total account level 3
					$grand_total_debet += $account_debet;
					$grand_total_credit += $account_credit;
					$grand_total_before += $account_saldo_before;
					  
						$grand_total_modal_debet += $account_debet;
						$grand_total_modal_credit += $account_credit;
						$grand_total_modal_before += $account_saldo_before;					
					
					//if($c->accounting_code == "3020001"){ echo "<b>$account_debet --- $account_credit</b>";}
					
					$neraca .= '<tr>     
								<td align="left" >  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c->accounting_code." ".$c->accounting_name.'</td>	
								<td class="text-right">'.($account_saldo_before < 0 ? "(".number_format(abs($account_saldo_before)).")" : number_format($account_saldo_before)).'</td>
								<td class="text-right">'.($account_debet < 0 ? "(".number_format(abs($account_debet)).")" : number_format($account_debet)).'</td>
								<td class="text-right">'.($account_credit < 0 ? "(".number_format(abs($account_credit)).")" : number_format($account_credit)).'</td>
								<td class="text-right">'.($account_saldo < 0 ? "(".number_format(abs($account_saldo)).")" : number_format($account_saldo)).'</td>
								</tr>';					
				}	
				$code_level0_old = $code_level0;
			endforeach; 
				$grand_total_modal_saldo = $grand_total_modal_before-$grand_total_modal_debet+$grand_total_modal_credit;
				$neraca .= '<tr bgcolor="#eee">     
							<td align="left" ><b>TOTAL MODAL</b></td>	
							<td class="text-right"><b>'.($grand_total_modal_before < 0 ? "(".number_format(abs($grand_total_modal_before)).")" : number_format($grand_total_modal_before)).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_debet < 0 ? "(".number_format(abs($grand_total_modal_debet)).")" : number_format($grand_total_modal_debet)).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_credit < 0 ? "(".number_format(abs($grand_total_modal_credit)).")" : number_format($grand_total_modal_credit)).'</b></td>
							<td class="text-right"><b>'.($grand_total_modal_saldo < 0 ? "(".number_format(abs($grand_total_modal_saldo)).")" : number_format($grand_total_modal_saldo)).'</b></td>
							</tr>';	
				
				
				
				//-----------	
				//GRAND TOTAL
				//-----------				
				$grand_total_saldo = $grand_total_aktiva_saldo - $grand_total_kewajiban_saldo - $grand_total_modal_saldo;
				$grand_total_before = $grand_total_aktiva_before - $grand_total_kewajiban_before - $grand_total_modal_before;
					$grand_total_before =0;	
					$grand_total_debet =0;	
					$grand_total_credit =0;						
				$neraca .= '<tfoot bgcolor="#ddd"><tr">     
							<td align="left" ><b>GRAND TOTAL</b></td>	
							<td class="text-right"><b>'.($grand_total_before < 0 ? "(".number_format(abs($grand_total_before)).")" : number_format($grand_total_before)).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_debet).'</b></td>
							<td class="text-right"><b>'.number_format($grand_total_credit).'</b></td>
							<td class="text-right"><b>'.($grand_total_saldo < 0 ? "(".number_format(abs($grand_total_saldo)).")" : number_format($grand_total_saldo)).'</b></td>
							</tr></tfoot>';	
			 
			$this->template	->set('menu_title', 'Neraca')
							->set('menu_jurnal', 'active')
							->set('accounting', $accounting)
							->set('neraca', $neraca)
							->build('neraca');

	}
	
	
}