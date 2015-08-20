<?php

class Operation_download extends Front_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('operation_model');
	}

	public function index($branch='', $startdate='', $enddate=''){
		//SAMPLE USAGE ACCESS CONTROL
		/*
		$mod    	= $this->router->fetch_module(); 
		$cont       = $this->router->fetch_class();
		$func       = $this->router->fetch_method();
		$user_level = $this->session->userdata('user_level');//1 Admin, 2 Manager, etc.

		$access     = $this->access_control->check_access($user_level, $mod, $cont, $func);
		*/
		//echo '$this->access_control->check_access('.$mod.', '.$cont.', '.$func.', '.$user_level.')';
		//echo 'access='.$access->access_id.$access->access_userbinding.$access->access_privilege.$access->access_level_max;
		//echo '<br/>'; var_dump($access);

		if($this->session->userdata('logged_in'))
		{
			if(true){//if(count($access)){

				if($branch != ''){
					if($startdate == '')
						{ $startdate = date('Y-m-d',strtotime('last day of previous month')); }
					if($enddate == '')
						{ $enddate = date('Y-m-d',strtotime('now')); }
				}
				else{
					$branch = '0';
					if($startdate == '')
						{ $startdate = date('Y-m-d',strtotime('last day of previous month')); }
					if($enddate == '')
						{ $enddate = date('Y-m-d',strtotime('now')); }
				}

				$total_anggota_awal  = $this->operation_model->count_clients_by_branch_by_date($branch, $startdate);
				$total_anggota_akhir = $this->operation_model->count_clients_by_branch_by_date($branch, $enddate);

				$total_majelis_awal  = $this->operation_model->count_majelis_by_branch_by_date($branch, $startdate);
				$total_majelis_akhir = $this->operation_model->count_majelis_by_branch_by_date($branch, $enddate);

				$total_cabang  = $this->operation_model->count_all_cabang();
				$total_officer = $this->operation_model->count_all_officer();

				$total_outstanding_pinjaman_awal = $this->operation_model->sum_all_outstanding_pinjaman_by_branch_by_date($branch, $startdate);
				$total_outstanding_pinjaman_akhir = $this->operation_model->sum_all_outstanding_pinjaman_by_branch_by_date($branch, $enddate);

				$total_saldo_tabsukarela_awal = $this->operation_model->sum_tabsukarela_by_branch_by_date($branch, $startdate);
				$total_saldo_tabsukarela_akhir = $this->operation_model->sum_tabsukarela_by_branch_by_date($branch, $enddate);
				
				$total_saldo_tabwajib_awal = $this->operation_model->sum_tabwajib_by_branch_by_date($branch, $startdate);
				$total_saldo_tabwajib_akhir = $this->operation_model->sum_tabwajib_by_branch_by_date($branch, $enddate);
				
				$total_saldo_tabberjangka_awal = $this->operation_model->sum_tabberjangka_by_branch_by_date($branch, $startdate);
				$total_saldo_tabberjangka_akhir = $this->operation_model->sum_tabberjangka_by_branch_by_date($branch, $enddate);

				$list_cabang   = $this->operation_model->list_cabang();

				$total_par_minggu1 = $this->operation_model->count_par_per_branch_per_week('0', $startdate, $enddate, '1');
				$total_par_minggu2 = $this->operation_model->count_par_per_branch_per_week('0', $startdate, $enddate, '2');
				$total_par_minggu3 = $this->operation_model->count_par_per_branch_per_week('0', $startdate, $enddate, '3');
				$total_par_minggu4 = $this->operation_model->count_par_per_branch_per_week('0', $startdate, $enddate, '4');

				$sum_par_minggu1 = $this->operation_model->sum_par_per_branch_per_week('0', $startdate, $enddate, '1');
				$sum_par_minggu2 = $this->operation_model->sum_par_per_branch_per_week('0', $startdate, $enddate, '2');
				$sum_par_minggu3 = $this->operation_model->sum_par_per_branch_per_week('0', $startdate, $enddate, '3');
				$sum_par_minggu4 = $this->operation_model->sum_par_per_branch_per_week('0', $startdate, $enddate, '4');

				for($i=0; $i<count($list_cabang); $i++){
					$total_anggota_per_cabang_awal[$i] = $this->operation_model->count_clients_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_anggota_per_cabang_akhir[$i] = $this->operation_model->count_clients_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);

					$total_majelis_per_cabang_awal[$i] = $this->operation_model->count_majelis_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_majelis_per_cabang_akhir[$i] = $this->operation_model->count_majelis_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);

					$total_outstanding_pinjaman_per_cabang_awal[$i] = $this->operation_model->sum_all_outstanding_pinjaman_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_outstanding_pinjaman_per_cabang_akhir[$i] = $this->operation_model->sum_all_outstanding_pinjaman_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);
					
					$total_saldo_tabsukarela_per_cabang_awal[$i] = $this->operation_model->sum_tabsukarela_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_saldo_tabsukarela_per_cabang_akhir[$i] = $this->operation_model->sum_tabsukarela_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);

					$total_saldo_tabwajib_per_cabang_awal[$i] = $this->operation_model->sum_tabwajib_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_saldo_tabwajib_per_cabang_akhir[$i] = $this->operation_model->sum_tabwajib_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);

					$total_saldo_tabberjangka_per_cabang_awal[$i] = $this->operation_model->sum_tabberjangka_by_branch_by_date($list_cabang[$i]['branch_id'], $startdate);
					$total_saldo_tabberjangka_per_cabang_akhir[$i] = $this->operation_model->sum_tabberjangka_by_branch_by_date($list_cabang[$i]['branch_id'], $enddate);
					
					$total_officer_per_cabang[$i] = $this->operation_model->count_all_officer_by_branch($list_cabang[$i]['branch_id']);

					$total_par_per_cabang_minggu1[$i] = $this->operation_model->count_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '1');
					$total_par_per_cabang_minggu2[$i] = $this->operation_model->count_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '2');
					$total_par_per_cabang_minggu3[$i] = $this->operation_model->count_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '3');
					$total_par_per_cabang_minggu4[$i] = $this->operation_model->count_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '4');

					$sum_par_per_cabang_minggu1[$i] = $this->operation_model->sum_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '1');
					$sum_par_per_cabang_minggu2[$i] = $this->operation_model->sum_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '2');
					$sum_par_per_cabang_minggu3[$i] = $this->operation_model->sum_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '3');
					$sum_par_per_cabang_minggu4[$i] = $this->operation_model->sum_par_per_branch_per_week($list_cabang[$i]['branch_id'], $startdate, $enddate, '4');
				}
				
				//SAMPLE USAGE ACTIVITY LOG
				$log_data = array(

						'activity_userid' 	    => $this->session->userdata['user_id'],
						'activity_userbranch'   => $this->session->userdata['user_branch'],
						'activity_module' 		=> $this->router->fetch_module(),
						'activity_controller'   => $this->router->fetch_class(),
						'activity_method'       => $this->router->fetch_method(),
						'activity_data'         => 'log_operation_download',
						'activity_remarks'      => 'log_remarks: no html'
				);
				$log = $this->access_control->log_activity($log_data);

				//==================

				//$filename="Operation_Report_$startdate_to_$enddate";
				$html .= '<div align="center">Operation Report Koperasi Amartha</div>';
				$html .= '<div align="center"><img src="'.FCPATH.'files/logo_amartha.png" /></div>';
				$html .= '<div align="center">Per '.date('j F Y', strtotime($startdate))
					  .' to '.date('j F Y', strtotime($enddate)).'</div>';
				$html .= '<div align="center">&nbsp;</div>';

				$html .= '<table class="table table-striped m-b-none text-sm">';      
				$html .= '<thead>';                  
				$html .= '<tr>';
				$html .= '<th width="102px" align="center">PARAMETERS</th>';
				$html .= '<th width="102px" align="center">&nbsp;</th>';
				
				for($i=0; $i<count($list_cabang); $i++) {
					$b = $i + 1;
					$html .= '<th width="102px" align="right"><b>'.strtoupper($list_cabang[$i]['branch_name']).'</b></th>';
				}
				
				$html .= '<th width="102px" align="right"><b> ALL</b></th>';
				$html .= '</tr>';                  
				$html .= '</thead>'; 
				$html .= '<tbody>';

				//==================

				//#ANGGOTA
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Anggota</td>';
				$html .= '<td width="102px" align="center">Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.$total_anggota_per_cabang_awal[$i].'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.array_sum($total_anggota_per_cabang_awal).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.$total_anggota_per_cabang_akhir[$i].'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.array_sum($total_anggota_per_cabang_akhir).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_anggota = array_sum($total_anggota_per_cabang_akhir)-array_sum($total_anggota_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.$total_mutasi_anggota.'</b></td>';
				$html .= '</tr>';

				//#MAJELIS
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Majelis</td>';
				$html .= '<td width="102px" align="center">Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.$total_majelis_per_cabang_awal[$i].'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.array_sum($total_majelis_per_cabang_awal).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.$total_majelis_per_cabang_akhir[$i].'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.array_sum($total_majelis_per_cabang_akhir).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_majelis = array_sum($total_majelis_per_cabang_akhir)-array_sum($total_majelis_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.$total_mutasi_majelis.'</b></td>';
				$html .= '</tr>';

				//#OS PINJAMAN
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">OS Pinjaman</td>';
				$html .= '<td width="102px" align="center">Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_awal[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_outstanding_pinjaman_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_akhir[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_os_pinjaman = array_sum($total_outstanding_pinjaman_per_cabang_akhir)-array_sum($total_outstanding_pinjaman_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.number_format($total_mutasi_os_pinjaman).'</b></td>';
				$html .= '</tr>';

				//#RERATA PINJAMAN
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Rerata Pinjaman</td>';
				$html .= '<td width="102px" align="center">Awal</td>';
				
				$akumulasi_rerata_os_awal = 0;
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_awal[$i]/$total_anggota_per_cabang_awal[$i]).'</td>';
					$akumulasi_rerata_os_awal = $akumulasi_rerata_os_awal + ($total_outstanding_pinjaman_per_cabang_awal[$i]/$total_anggota_per_cabang_awal[$i]);
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_outstanding_pinjaman_per_cabang_awal)/array_sum($total_anggota_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Akhir</td>';
				
				$akumulasi_rerata_os_akhir = 0;
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_akhir[$i]/$total_anggota_per_cabang_akhir[$i]).'</td>';
					$akumulasi_rerata_os_akhir = $akumulasi_rerata_os_akhir + ($total_outstanding_pinjaman_per_cabang_akhir[$i]/$total_anggota_per_cabang_akhir[$i]);
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)/array_sum($total_anggota_per_cabang_akhir)).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$awal = array_sum($total_outstanding_pinjaman_per_cabang_awal)/array_sum($total_anggota_per_cabang_awal);
				$akhir = array_sum($total_outstanding_pinjaman_per_cabang_akhir)/array_sum($total_anggota_per_cabang_akhir);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.number_format($akhir-$awal).'</b></td>';
				$html .= '</tr>';

				//#KOLEKTABILITAS (PAR) NASABAH
				//MINGGU1
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Kolektibilitas/PAR Nasabah</td>';
				$html .= '<td width="102px" align="center">Minggu 1</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_par_per_cabang_minggu1[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_par_per_cabang_minggu1)).'</b></td>';
				$html .= '</tr>';

				//MINGGU2
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu 2</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_par_per_cabang_minggu2[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_par_per_cabang_minggu2)).'</b></td>';
				$html .= '</tr>';

				//MINGGU3
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu 3</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_par_per_cabang_minggu3[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_par_per_cabang_minggu3)).'</b></td>';
				$html .= '</tr>';

				//MINGGU4
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu > 3</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_par_per_cabang_minggu4[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_par_per_cabang_minggu4)).'</b></td>';
				$html .= '</tr>';

				//#KOLEKTABILITAS (PAR) OUTSTANDING
				//MINGGU1
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Kolektibilitas/PAR Outstanding</td>';
				$html .= '<td width="102px" align="center">Minggu 1</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($sum_par_per_cabang_minggu1[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($sum_par_per_cabang_minggu1)).'</b></td>';
				$html .= '</tr>';

				//MINGGU2
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu 2</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($sum_par_per_cabang_minggu2[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($sum_par_per_cabang_minggu2)).'</b></td>';
				$html .= '</tr>';

				//MINGGU3
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu 3</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($sum_par_per_cabang_minggu3[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($sum_par_per_cabang_minggu3)).'</b></td>';
				$html .= '</tr>';

				//MINGGU4
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Minggu > 3</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($sum_par_per_cabang_minggu4[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($sum_par_per_cabang_minggu4)).'</b></td>';
				$html .= '</tr>';

				//DIVIDER//
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';

				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td width="102px" align="center">&nbsp;</td>';
				}
				
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '</tr>';

				//#SALDO TAB SUKARELA
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Tab Sukarela</td>';
				$html .= '<td width="102px" align="center">Saldo Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabsukarela_per_cabang_awal[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabsukarela_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Saldo Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabsukarela_per_cabang_akhir[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabsukarela_per_cabang_akhir)).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_tab_sukarela = array_sum($total_saldo_tabsukarela_per_cabang_akhir)-array_sum($total_saldo_tabsukarela_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.number_format($total_mutasi_tab_sukarela).'</b></td>';
				$html .= '</tr>';

				//#SALDO TAB WAJIB
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Tab Wajib</td>';
				$html .= '<td width="102px" align="center">Saldo Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabwajib_per_cabang_awal[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabwajib_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Saldo Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabwajib_per_cabang_akhir[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabwajib_per_cabang_akhir)).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_tab_wajib = array_sum($total_saldo_tabwajib_per_cabang_akhir)-array_sum($total_saldo_tabwajib_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.number_format($total_mutasi_tab_wajib).'</b></td>';
				$html .= '</tr>';

				//#SALDO TAB BERJANGKA
				//AWAL
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Tab Berjangka</td>';
				$html .= '<td width="102px" align="center">Saldo Awal</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabberjangka_per_cabang_awal[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabberjangka_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//AKHIR
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Saldo Akhir</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.number_format($total_saldo_tabberjangka_per_cabang_awal[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.number_format(array_sum($total_saldo_tabberjangka_per_cabang_awal)).'</b></td>';
				$html .= '</tr>';

				//MUTASI
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					if($i == count($list_cabang) - 1)
						$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;">Mutasi</td>';
					else
						$html .= '<td width="102px" align="right">&nbsp;</td>';
				}

				$total_mutasi_tab_berjangka = array_sum($total_saldo_tabberjangka_per_cabang_akhir)-array_sum($total_saldo_tabberjangka_per_cabang_awal);	
				$html .= '<td width="102px" align="right" style="background-color:#AAA8AA;"><b>'.number_format($total_mutasi_tab_berjangka).'</b></td>';
				$html .= '</tr>';

				//#RASIO FO
				//JUMLAH FO
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">Rasio FO</td>';
				$html .= '<td width="102px" align="center">Jumlah FO</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.$total_officer_per_cabang[$i].'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.array_sum($total_officer_per_cabang).'</b></td>';
				$html .= '</tr>';

				//MAJELIS PER FO
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Majelis per FO</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.round($total_majelis_per_cabang_akhir[$i]/$total_officer_per_cabang[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.round(array_sum($total_majelis_per_cabang_akhir)/array_sum($total_officer_per_cabang)).'</b></td>';
				$html .= '</tr>';

				//ANGGOTA PER FO
				$html .= '<tr>';
				$html .= '<td width="102px" align="center">&nbsp;</td>';
				$html .= '<td width="102px" align="center">Anggota per FO</td>';
				
				for($i=0; $i<count($list_cabang); $i++) { 
					$html .= '<td align="right">'.round($total_anggota_per_cabang_akhir[$i]/$total_officer_per_cabang[$i]).'</td>';
				}	
				$html .= '<td width="102px" align="right"><b>'.round(array_sum($total_anggota_per_cabang_akhir)/array_sum($total_officer_per_cabang)).'</b></td>';
				$html .= '</tr>';

				//==================
			


				//==================			
				$html .= '</tbody>';
				$html .= '</table>';

				//echo $html;
				$this->load->library('mpdf');
				$mpdf=new mPDF('utf-8', 'A4-L');
				//$mpdf->SetFooter("Top Sheet".'||{PAGENO}|'); 
				$mpdf->WriteHTML($html);
				$mpdf->Output();
				//$pdfFilePath = FCPATH."downloads/reports/operation/$branch/$filename.pdf";
				//$pdffile = base_url()."downloads/reports/operation/$branch/$filename.pdf";
				//$mpdf->Output($pdfFilePath,'F');
				//redirect($pdffile, 'refresh');
				//echo $html;

							
			}else{
				redirect('/', 'refresh');
			}

		}else{
			redirect('login', 'refresh');
		}
	}

}