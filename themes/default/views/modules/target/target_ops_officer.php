<section class="main">
	<div class="container">
	
	<div id="module_title">
			<div class="m-b-md"><h3 class="m-b-none"><?php echo $menu_title; ?></h3></div>
	</div>
	
	<?php if($this->session->flashdata('message')){ ?>
			<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button> <?php echo print_message($this->session->flashdata('message')); ?></div>
	<?php } ?>
		
	<section class="panel panel-default">
			<!-- TABLE HEADER -->
			<div class="row text-sm wrapper">
				<div class="col-sm-4 m-b-xs">
					
				</div>
				<div class="col-sm-3 pull-right">
					<div class="input-group">
						
					</div>
				</div>
			</div>
			
			<div class="table-responsive">  
				
				<table class="table table-striped m-b-none text-sm">              
					<thead>                  
					  <tr>
						<th width="30px">No</th>
						<th>Nama Target</th>
						<th>Item Target</th>
						<th>Cabang</th>
						<th>Nama FO</th>
						<th class="text-right">Nilai Target</th>
						<th class="text-right">Nilai Total Target</th>
						<th>Tanggal Awal</th>
						<th>Tanggal Akhir</th>
						<th width="80px" class="text-center">Manage</th>
					  </tr>                  
					</thead> 
					<tbody>	
					<?php
						$total_rows=$config['total_rows'];
						if(empty($no)){ 
							$no=1; 
							$nostart=1;
							$noend=$config['per_page'];
							if($noend>$total_rows){ $noend=$total_rows; }
						}else{ 
							$no=$no+1;
							$nostart=$no;
							$noend=$nostart+$config['per_page']-1;
							if($noend>$total_rows){ $noend=$total_rows; }
						} 
					?>
					<?php foreach($target as $t):  ?>
						<tr>     
							<td align="center"><?php echo $no; ?></td>
							<td><?php echo ucfirst($t->target_category); ?></td>
							<td><?php 
									if($t->target_item == NULL) 
										echo 'N/A';
									else if ($t->target_item == 'PBYN')
										echo 'Pembiayaan';
									else if ($t->target_item == 'OTBJ')
										echo 'OS Tab Berjangka';
									else
										echo '-'; ?></td>
							<td class="hidden">
								<?php
									if($t->officer_name == NULL) 
										echo 'Management';
									else
										echo ucfirst($t->officer_name); ?>
								</td>
							<td><?php 
									if($t->branch_name == NULL) 
										echo 'Kantor Pusat';
									else
										echo ucfirst($t->branch_name); ?>
									</td>
							
							<td><?php echo $t->officer_name; ?></td>
							<td class="text-right"><?php echo number_format($t->target_officer_amount); ?></td>
							<td class="text-right"><?php echo number_format($t->target_amount); ?></td>
							<td><?php echo date('d M Y', strtotime($t->target_startdate)); ?></td>
							<td><?php echo date('d M Y', strtotime($t->target_enddate)); ?></td>
							<td class="text-center">
								<a href="<?php echo site_url()."/target/target_ops_disposisi_edit/".$t->target_officer_id; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
								<a href="<?php echo site_url()."/target/target_ops_disposisi_delete/".$t->target_officer_id; ?>" title="Delete" onclick="return confirmDialog();" ><i class="fa fa-trash-o"></i></a></td>
						</tr>
					<?php $no++; endforeach; ?>
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