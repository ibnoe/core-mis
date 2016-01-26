<section class="main">
	<div class="container">
			
		<div id="module_title">
			<div class="m-b-md">
				<div class="row">
					<div class="col-md-6">
						<h3 class="m-b-none"><?php echo $menu_title; ?></h3>
						<h4 class="m-b-none"><?php echo $client->client_fullname; ?> - <?php echo $client->client_account; ?></h4>
					</div>
					<div class="col-md-6 pull-right text-right">
						<br/><br/>
						
					</div>
				</div>				
			</div>
		</div>
		
		<?php if($this->session->flashdata('message')){ ?>
				<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> <?php echo print_message($this->session->flashdata('message')); ?></div>
		<?php } ?>
					
		<section class="panel panel-default">
			
			
			<!-- TABLE BODY -->
			<div class="table-responsive">
					<table class="table table-striped m-b-none text-sm">      
						<thead>                  
						  <tr>
							<th width="30px">No</th>
							<th width="180px">TS Code</th>
							<th width="150px">Tanggal</th>
							<th class="text-right">Debet (Rp)</th>
							<th class="text-right">Credit (Rp)</th>
							<th class="text-right">Saldo (Rp)</th>
							<th>Remark</th>
						  </tr>                  
						</thead> 
						<tbody>	
						<?php
							if(empty($no)){ 
									$no=1; 
									$nostart=1;
									$noend=$config['per_page'];
									$totalrow = $config['total_rows'];
									if($noend > $totalrow) {$noend=$totalrow;}
								}else{ 
									$no=$no+1;
									$nostart=$no;
									$noend=$nostart+$config['per_page']-1;
									$totalrow = $config['total_rows'];
									if($noend > $totalrow) {$noend=$totalrow;}
								} 
						?>
						<?php foreach($data as $c):  ?>
							<tr>     
								<td align="center"><?php echo $no; ?></td>	
								<td><?php if($c->tr_topsheet_code != 0) { echo $c->tr_topsheet_code; }else{ echo $c->tr_transactioncode; }; ?></td>				              
								<td><?php echo date("Y-m-d", strtotime($c->tr_date)); ?></td>
								<td class="text-right"><?php echo number_format($c->tr_debet); ?></td>
								<td class="text-right"><?php echo number_format($c->tr_credit); ?></td>
								<td class="text-right"><?php echo number_format($c->tr_saldo); ?></td>
								<td><?php echo $c->tr_remark; ?></td>
								
							</tr>
							
						<?php $no++; endforeach; ?>
						<?php echo $list;?>
						</tbody>	
					</table>  
					
				</div>
				
			</section>
		</div>
</section>