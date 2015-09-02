<?php 
$user_level = $this->session->userdata('user_level');
?>

<section class="main">
	<div class="container">
			
		<div id="module_title">
			<div class="m-b-md"><h3 class="m-b-none"><?php echo $menu_title; ?></h3></div>
		</div>
		
		<?php if($this->session->flashdata('message')){ ?>
				<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> <?php echo print_message($this->session->flashdata('message')); ?></div>
		<?php } ?>
					
		<section class="panel panel-default">
			<!-- TABLE HEADER -->
			<div class="row text-sm wrapper">
				<div class="col-sm-4 m-b-xs">
				</div>
				
				<!-- SEARCH FORM -->
				<form action="" method="post"> 
					<div class="col-sm-4 m-b-xs pull-right text-right">
						<select name="key" class="input-sm form-control input-s-sm inline">
							<option value="fullname">Nama </option>
							<option value="account">No Rekening</option>
						</select>
						<input type="text" name="q" class="input-sm form-control input-s-sm inline" placeholder="Search">
						<button class="btn btn-sm btn-default" type="submit">Go!</button>
					</div>
				</form>					
			</div>
			
			<!-- TABLE BODY -->
			<div class="table-responsive">
					<table class="table table-striped m-b-none text-sm">      
						<thead>                  
						  <tr>
							<th width="30px">No</th>
							<th width="110px">No. Rekening</th>
							<th>Nama Lengkap</th>
							<?php if($this->session->userdata('user_branch') == 0){ ?><th class="text-center">Cabang</th><?php } ?>
							<th class="text-center">Majelis</th>
							<th class="text-center">Plafond</th>
							<th class="text-center">Ke</th>
							<th class="text-center">Tgl Pengajuan</th>
							<th class="text-center">Tgl Pencairan</th>
							<th class="text-center">Sektor</th>
							<th class="text-left">PPI</th>
							<th class="text-left">CHI</th>
							<th class="text-left">Status<br/>Pembiayaan</th>
							<th class="text-left">Status<br/>Pengajuan</th>
							</tr>                  
						</thead> 
						<tbody>	
						<?php
							if(empty($no)){ 
									$no=1; 
									$nostart=1;
									$noend=$config['per_page'];
								}else{ 
									$no=$no+1;
									$nostart=$no;
									$noend=$nostart+$config['per_page']-1;
								} 
						?>
						<?php foreach($clients as $c):  ?>
							<tr>     
								<td align="center"><?php echo $no; ?></td>					              
								<td><?php echo $c->client_account; ?></td>
								<td><a href="<?php echo site_url()."/clients/summary/".$c->client_id; ?>" title="View This Client"><?php echo $c->client_fullname; ?></a></td>
								<?php if($this->session->userdata('user_branch') == 0){ ?><td class="text-center"><?php echo $c->branch_name; ?></td><?php } ?>
								<td class="text-center"><a href="<?php echo site_url()."/group/view/".$c->client_group; ?>" title="View This Group"><?php echo $c->group_name; ?></a></td>
								<td class="text-right" align="right"><?php echo number_format($c->data_plafond); ?></td>
								<td class="text-center"><?php echo $c->client_pembiayaan; ?></td>
								<td class="text-center"><?php echo $c->data_tgl; ?></td>
								<td class="text-center"><?php echo $c->data_date_accept; ?></td>
								<td class="text-center"><?php echo $c->sector_name; ?></td>								
								<td class="text-center"><?php echo $c->data_popi_total; ?> (<?php echo $c->data_popi_kategori; ?>)</td>
								<td class="text-center"><?php echo $c->data_rmc_total; ?> (<?php echo $c->data_rmc_kategori; ?>)</td>
								<td class="text-center">
									<?php 
										if($c->data_status == "1"){ echo "Berjalan"; }
										elseif($c->data_status == "2"){ echo "Pengajuan"; }
										elseif($c->data_status == "3"){ echo "Selesai"; }
										elseif($c->data_status == "4"){ echo "<a href='' title='".$c->data_alasan."'>Gagal Droping</a>"; }
									?>
								</td>
								<td class="text-left">
									<?php 
										if($c->data_status_pengajuan == "v"){ echo "Disetujui"; } 
										elseif($c->data_status_pengajuan == "x"){ echo "Dtunda"; }
										elseif($c->data_status_pengajuan == "k"){ echo "Komite"; } 
									?> 
								</td>
								
							</tr>
							
						<?php $no++; endforeach; ?>
						<?php echo $list;?>
						</tbody>	
					</table>  
					
				</div>
				
				<footer class="panel-footer">
					<div class="row">
						<div class="col-sm-4 text-left"> <small class="text-muted inline m-t-sm m-b-sm">showing <?php echo $nostart; ?>-<?php echo $noend; ?> of <?php echo $config['total_rows']; ?> items</small></div>
						<div class="col-sm-5 text-right text-center-xs pull-right">
							<ul class="pagination pagination-sm m-t-none m-b-none">
								<?php echo $this->pagination->create_links(); ?>
							</ul>
						</div>
					</div>
				</footer>
			</section>
		</div>
</section>

<script type="text/javascript">
	$(document).ready(function() {
		<?php for ($i=1;$i<$no;$i++){ ?>
			$("#status_pengajuan_<?php echo $i; ?>").change(function() { 
				  var form_data = { status: $("#status_pengajuan_<?php echo $i; ?>").val() , data_id : $("#data_id_<?php echo $i; ?>").val()};		  
				  $.ajax({
						url: "<?php echo site_url('branch/update_status_pengajuan'); ?>",
						type: 'POST',
						dataType: 'json',
						data: form_data,
						statusCode: 
							{ 200: function() {
								var hasil ="<i class='fa fa-check text-success'></i>";
								$("#result_<?php echo $i; ?>").html(hasil);
								}
							}
						
				 });
			});	
		
		<?php } ?>
	});
</script>