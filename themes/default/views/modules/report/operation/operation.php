							<?php //var_dump($total_par_minggu1); echo '<br/>'; ?>
							<?php //echo 'buffer='.$this->router->fetch_module().'-'.$this->router->fetch_class().'-'.$this->router->fetch_method(); ?>
							<ul class="breadcrumb no-border no-radius b-b b-light pull-in">
								<li><a href=""><i class="fa fa-home"></i> Home</a></li>
								<li class="active">Operational Report</li>
							</ul>
							<div class="m-b-md">
								<h3 class="m-b-none">Operational Report</h3>
							</div>
							<section class="panel panel-default">
								<div class="row m-l-none m-r-none bg-light lter">
									<div class="col-sm-6 col-md-3 padder-v b-r b-light"> <span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-info"></i> <i class="fa fa-male fa-stack-1x text-white"></i> </span> 
										<a class="clear" href="#"> <span class="h3 block m-t-xs"><strong><?php echo $total_all_anggota_akhir; ?></strong></span>  <small class="text-muted text-uc">ANGGOTA</small> 
										</a>
									</div>
									<div class="col-sm-6 col-md-3 padder-v b-r b-light lt"> <span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-warning"></i> <i class="fa fa-group fa-stack-1x text-white"></i> </span>
										<a
										class="clear" href="#"> <span class="h3 block m-t-xs"><strong id="bugs"><?php echo $total_all_majelis_akhir; ?></strong></span>  <small class="text-muted text-uc">MAJELIS</small> 
											</a>
									</div>
									<div class="col-sm-6 col-md-3 padder-v b-r b-light"> <span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x text-danger"></i> <i class="fa fa-building-o fa-stack-1x text-white"></i> <!--<span class="easypiechart pos-abt" data-percent="100" data-line-width="4" data-track-Color="#f5f5f5" data-scale-Color="false" data-size="50" data-line-cap='butt' data-animate="3000" data-target="#firers" data-update="5000"></span>--> </span>
										<a
										class="clear" href="#"> <span class="h3 block m-t-xs"><strong id="firers"><?php echo $total_all_cabang; ?></strong></span>  <small class="text-muted text-uc">CABANG</small> 
											</a>
									</div>
									<div class="col-sm-6 col-md-3 padder-v b-r b-light lt"> <span class="fa-stack fa-2x pull-left m-r-sm"> <i class="fa fa-circle fa-stack-2x icon-muted"></i> <i class="fa fa-briefcase fa-stack-1x text-white"></i> </span> 
										<a class="clear" href="#"> <span class="h3 block m-t-xs"><strong><?php echo $total_all_officer; ?></strong></span>  <small class="text-muted text-uc">PENDAMPING</small> 
										</a>
									</div>
								</div>
							</section>
							<div class="row">
								<div class="cold-md-12">
									<div class="col-sm-6 m-b-xs text-justify">
											<a href="<?php echo base_url().'index.php/report/operation_download/index/'.$branch.'/'.$startdate.'/'.$enddate; ?>" class="btn btn-sm btn-info" target="_blank">
											Download Operation Report</a>
									</div>
									<div class="col-sm-6 m-b-xs text-right">
										<form method="post" action="">
										<input type="hidden" name="filter" value="1">
										<input type="hidden" name="branch" value="0">
										<input type="text" name="startdate" class="datepicker-input inp90" data-date-format="yyyy-mm-dd" placeholder="Start Date">
										<input type="text" name="enddate" class="datepicker-input inp90" data-date-format="yyyy-mm-dd" placeholder="End Date">
										<button type="submit" name="submit" class="btn btn-xs btn-info">Filter</button> 
										</form>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="cold-md-12">
									&nbsp;
								</div>
							</div>
							<div class="row">
								<!-- SECTION RIGHT -->
								<div class="col-md-12">
									<!-- TABEL P.A.R -->
									<section class="panel panel-default">
										<header class="panel-heading font-bold">OPERATIONAL REVIEW REPORT PER <span style="color: blue;"><?php echo strtoupper($date_awal).'</span> TO <span style="color: blue;">'.strtoupper($date_akhir); ?></span></header>
										<div class="panel-body">
											<div>
												
												<table class="table table-striped m-b-none text-sm"> 
													<tr>
														<td width="10px"></td>
														<td width="" align=""></td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php $b = $i + 1;
															  echo '<td width="102px" align="right"><a href="'.site_url("report/operation_branch/overview/$b").'" target="_blank">';
															  echo '<b>'.strtoupper($list_cabang[$i]['branch_name']).'</b></a></td>'; ?>
														<?php } ?>
														<td width="102px" align="right"><b> ALL</b></td>
													</tr>
													<tr>
														<td><b>1</b></td>
														<td colspan="7"><b>ANGGOTA</b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. Anggota Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_anggota_per_cabang_awal[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_anggota_awal; ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. Anggota Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_anggota_per_cabang_akhir[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_anggota_akhir; ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_anggota_akhir-$total_all_anggota_awal; ?></b></td>
													</tr>
													<tr>
														<td><b>2</b></td>
														<td colspan="7"><b>MAJELIS</b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. Majelis Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_majelis_per_cabang_awal[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_majelis_awal; ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. Majelis Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_majelis_per_cabang_akhir[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_majelis_akhir; ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_all_majelis_akhir-$total_all_majelis_awal; ?></b></td>
													</tr>
													<tr>
														<td><b>3</b></td>
														<td colspan="4"><b>OUTSTANDING PINJAMAN</b></td>
														<td colspan="2"><b><?php echo "Rp ".number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. OS Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_awal[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_outstanding_pinjaman_per_cabang_awal)); ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. OS Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_akhir[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo "Rp ".number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)-array_sum($total_outstanding_pinjaman_per_cabang_awal)); ?></b></td>
													</tr>
													<tr>
														<td><b>4</b></td>
														<td colspan="4"><b>OUTSTANDING TABUNGAN SUKARELA</b></td>
														<td colspan="2"><b><?php echo "Rp ".number_format($total_saldo_tabsukarela_akhir); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. OS Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabsukarela_per_cabang_awal[$i]).'</td>' ?>
														<?php } ?>
														<td align="right">
															<b><?php echo number_format(array_sum($total_saldo_tabsukarela_per_cabang_awal));
																	 //$total_saldo_tabsukarela_per_lastmonth; ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. OS Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabsukarela_per_cabang_akhir[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabsukarela_per_cabang_akhir));
																						//$total_saldo_tabsukarela); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabsukarela_per_cabang_akhir)-array_sum($total_saldo_tabsukarela_per_cabang_awal))
																				   //$total_saldo_tabsukarela-$total_saldo_tabsukarela_per_lastmonth ?></b></td>
													</tr>												
													<tr>
														<td><b>5</b></td>
														<td colspan="4"><b>OUTSTANDING TABUNGAN BERJANGKA</b></td>
														<td colspan="2"><b><?php echo "Rp ".number_format($total_saldo_tabberjangka_akhir); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. OS Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabberjangka_per_cabang_awal[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabberjangka_per_cabang_awal)); 
																						//$total_saldo_tabberjangka_per_lastmonth) ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. OS Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabberjangka_per_cabang_akhir[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabberjangka_per_cabang_akhir));
																						//$total_saldo_tabberjangka ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabberjangka_per_cabang_akhir)-array_sum($total_saldo_tabberjangka_per_cabang_awal));
																						//$total_saldo_tabberjangka-$total_saldo_tabberjangka_per_lastmonth ?></b></td>
													</tr>
													<tr>
														<td><b>6</b></td>
														<td colspan="4"><b>OUTSTANDING TABUNGAN WAJIB</b></td>
														<td colspan="2"><b><?php echo "Rp ".number_format($total_saldo_tabwajib_akhir); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. OS Awal</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabwajib_per_cabang_awal[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabwajib_per_cabang_awal));
																						//$total_saldo_tabwajib_per_lastmonth ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. OS Akhir</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_saldo_tabwajib_per_cabang_akhir[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabwajib_per_cabang_akhir));
																						//$total_saldo_tabwajib); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format(array_sum($total_saldo_tabwajib_per_cabang_akhir)-array_sum($total_saldo_tabwajib_per_cabang_awal));
																						//$total_saldo_tabwajib-$total_saldo_tabwajib_per_lastmonth); ?></b></td>
													</tr>	
													<tr>
														<td><b>7</b></td>
														<td colspan="4"><b>RATA-RATA PINJAMAN</b></td>
														<td colspan="2"><b><?php //echo "Rp ".number_format($total_saldo_tabwajib); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. Rerata OS Awal</td>
														<?php $akumulasi_rerata_os_awal = 0;?>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_awal[$i]/$total_anggota_per_cabang_awal[$i]).'</td>' ?>
														<?php $akumulasi_rerata_os_awal = $akumulasi_rerata_os_awal + ($total_outstanding_pinjaman_per_cabang_awal[$i]/$total_anggota_per_cabang_awal[$i]); ?>
														<?php } ?>
														<td align="right"><b><?php echo //number_format($akumulasi_rerata_os_awal); 
																						number_format(array_sum($total_outstanding_pinjaman_per_cabang_awal)/array_sum($total_anggota_per_cabang_awal));
																						//$total_saldo_tabwajib_per_lastmonth ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. Rerata OS Akhir</td>
														<?php $akumulasi_rerata_os_akhir = 0;?>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($total_outstanding_pinjaman_per_cabang_akhir[$i]/$total_anggota_per_cabang_akhir[$i]).'</td>' ?>
														<?php $akumulasi_rerata_os_akhir = $akumulasi_rerata_os_akhir + ($total_outstanding_pinjaman_per_cabang_akhir[$i]/$total_anggota_per_cabang_akhir[$i]); ?>
														<?php } ?>
														<td align="right"><b><?php echo //number_format($akumulasi_rerata_os_akhir);
																						number_format(array_sum($total_outstanding_pinjaman_per_cabang_akhir)/array_sum($total_anggota_per_cabang_akhir));
																						//$total_saldo_tabwajib); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Mutasi</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right"></td>' ?>
														<?php } ?>
														<td align="right"><b><?php 		$awal = array_sum($total_outstanding_pinjaman_per_cabang_awal)/array_sum($total_anggota_per_cabang_awal);
																						$akhir = array_sum($total_outstanding_pinjaman_per_cabang_akhir)/array_sum($total_anggota_per_cabang_akhir);
																						echo //number_format($akumulasi_rerata_os_akhir-$akumulasi_rerata_os_awal);
																						'Rp '.number_format($akhir-$awal);
																						//$total_saldo_tabwajib-$total_saldo_tabwajib_per_lastmonth; ?></b></td>
													</tr>
													<tr>
														<td><b>8</b></td>
														<td colspan="4"><b>PENCAIRAN</b></td>
														<td colspan="2"><b><?php //echo "Rp ".number_format($total_saldo_tabwajib); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. Target Pencairan</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php $total_target_pencairan_per_cabang += $target_pencairan_per_cabang[$i]; ?>
														<?php echo '<td align="right">'.number_format($target_pencairan_per_cabang[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($total_target_pencairan_per_cabang); ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. Realisasi Pencairan</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php $total_realisasi_pencairan_per_cabang += $realisasi_pencairan_per_cabang[$i]; ?>
														<?php echo '<td align="right">'.number_format($realisasi_pencairan_per_cabang[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($total_realisasi_pencairan_per_cabang); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>c. Pencapaian Pencairan(%)</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($pencapaian_pencairan_per_cabang[$i], 0).'%</td>' ?>
														<?php } ?>
														<?php $total_pencapaian_pencairan_per_cabang = $total_realisasi_pencairan_per_cabang / $total_target_pencairan_per_cabang * 100; ?>
														<td align="right"><b><?php echo number_format($total_pencapaian_pencairan_per_cabang,0); ?>%</b></td>
													</tr>
													<tr>
														<td><b>9</b></td>
														<td colspan="4"><b>KOLEKTABILITAS PINJAMAN (PAR)</b></td>
														<td colspan="2"><b><?php //echo "Rp ".number_format($total_saldo_tabwajib); ?></b></td>
													</tr>												
													<tr>
														<td>NASABAH</td>
														<td>Minggu 1</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_par_per_cabang_minggu1[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_par_minggu1; ?></b></td>
													</tr>	
													<tr>
														<td></td>
														<td>Minggu 2</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_par_per_cabang_minggu2[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_par_minggu2; ?></b></td>
													</tr>	
													<tr>
														<td></td>
														<td>Minggu 3</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_par_per_cabang_minggu3[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_par_minggu3; ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Minggu > 3</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_par_per_cabang_minggu4[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo $total_par_minggu4; ?></b></td>
													</tr>									
													<tr>
														<td>OUTSTANDING</td>
														<td>Minggu 1</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($sum_par_per_cabang_minggu1[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($sum_par_minggu1); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Minggu 2</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($sum_par_per_cabang_minggu2[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($sum_par_minggu2); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Minggu 3</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($sum_par_per_cabang_minggu3[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($sum_par_minggu3); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>Minggu > 3</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.number_format($sum_par_per_cabang_minggu4[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo number_format($sum_par_minggu4); ?></b></td>
													</tr>
													<tr>
														<td><b>10</b></td>
														<td colspan="4"><b>RASIO FO</b></td>
														<td colspan="2"><b><?php //echo "Rp ".number_format($total_saldo_tabwajib); ?></b></td>
													</tr>												
													<tr>
														<td></td>
														<td>a. Jumlah FO</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.$total_officer_per_cabang[$i].'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo array_sum($total_officer_per_cabang); ?></b></td>
													</tr>											
													<tr>
														<td></td>
														<td>b. Majelis per FO </td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.round($total_majelis_per_cabang_akhir[$i]/$total_officer_per_cabang[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo round(array_sum($total_majelis_per_cabang_akhir)/array_sum($total_officer_per_cabang)); ?></b></td>
													</tr>
													<tr>
														<td></td>
														<td>c. Anggota per FO</td>
														<?php for($i=0; $i<count($list_cabang); $i++) { ?>
														<?php echo '<td align="right">'.round($total_anggota_per_cabang_akhir[$i]/$total_officer_per_cabang[$i]).'</td>' ?>
														<?php } ?>
														<td align="right"><b><?php echo round(array_sum($total_anggota_per_cabang_akhir)/array_sum($total_officer_per_cabang)); ?></b></td>
													</tr>
												</table>
											</div>
										</div>
									</section>
								</div>
								
								
							</div>
<script>
$(function(){

  // 
  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  var d1 = [];
  //for (var i = 0; i <= 11; i += 1) {
  //  d1.push([i,i]);
  //}
   d1.push([1,<?php echo $total_clients_weekly[4]; ?>]);
   d1.push([2,<?php echo $total_clients_weekly[3]; ?>]);
   d1.push([3,<?php echo $total_clients_weekly[2]; ?>]);
   d1.push([4,<?php echo $total_clients_weekly[1]; ?>]);
   
  $("#flot-1ine").length && $.plot($("#flot-1ine"), [{
          data: d1
      }], 
      {
        series: {
            lines: {
                show: true,
                lineWidth: 2,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.0
                    }, {
                        opacity: 0.2
                    }]
                }
            },
            points: {
                radius: 5,
                show: true
            },
            grow: {
              active: true,
              steps: 50
            },
            shadowSize: 2
        },
        grid: {
            hoverable: true,
            clickable: true,
            tickColor: "#f0f0f0",
            borderWidth: 1,
            color: '#f0f0f0'
        },
        colors: ["#65bd77"],
        xaxis:{
        },
        yaxis: {
          ticks: 5
        },
        tooltip: true,
        tooltipOpts: {
          content: "Minggu: %x : %y",
          defaultTheme: false,
          shifts: {
            x: 0,
            y: 20
          }
        }
      }
  );
  
  

});
</script>