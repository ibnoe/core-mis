<section class="main">
	<div class="container">
	
		<div id="module_title">
			<div class="m-b-md"><h3 class="m-b-none"><?php echo $menu_title; ?></h3></div>			
		</div>
									
			<div class="panel panel-default">
			
				<!-- Panel Head -->
				<div class="panel-heading">
					
				</div>
				
				<div class="row text-sm wrapper">
				<div class="col-sm-4 m-b-xs">
					<?php echo "<b>DATE : </b>  ".$date_start." s/d ".$date_end; ?>
				</div>
				<div class="col-sm-3 pull-right">
					<div class="input-group">
						<form method="post" action="">
						<input type="text" name="date_start" class="datepicker-input inp90" data-date-format="yyyy-mm-dd" placeholder="Start Date" />
						<input type="text" name="date_end" class="datepicker-input inp90" data-date-format="yyyy-mm-dd" placeholder="End Date" /> 
						<button type="submit" class="btn btn-xs btn-info" >Submit</button>	
						</form>
					</div>
				</div>
			</div>
				
				<!-- Panel Body -->
				<div class="panel-body">
					<!-- Tab panes -->
					<div class="tab-content">
						
						<!-- ANGGOTA -->
							<div class="table-responsive">
								<table class="table table-striped m-b-none text-sm">      
									<thead>                  
									  <tr>
										<th width="30px">No</th>
										<th>Cabang</th>
										<th class="text-right">Hadir</th>
										<th class="text-right">Sakit</th>
										<th class="text-right">Cuti</th>
										<th class="text-right">Ijin</th>
										<th class="text-right">Alpha</th>
										<th class="text-right">Kehadiran (%)</th>
									  </tr>                  
									</thead> 
									<tbody>	
									<?php $no=1;?>
									<?php foreach($branch as $b):  ?>
										<tr>     
											<td align="center"><?php echo $no; ?></td>					              
											<td><?php echo $b->branch_name; ?></td>
											<?php
												$presence_h = $this->presence_model->count_presence("h", $b->branch_id, $date_start, $date_end);
												$presence_s = $this->presence_model->count_presence("s", $b->branch_id, $date_start, $date_end);
												$presence_c = $this->presence_model->count_presence("c", $b->branch_id, $date_start, $date_end);
												$presence_i = $this->presence_model->count_presence("i", $b->branch_id, $date_start, $date_end);
												$presence_a = $this->presence_model->count_presence("a", $b->branch_id, $date_start, $date_end);
												
												$presentase = $presence_h / ($presence_h + $presence_s + $presence_c + $presence_i + $presence_a) * 100;
											 ?>
											 <td align="right"><?php echo $presence_h; ?></td>
											 <td align="right"><?php echo $presence_s; ?></td>
											 <td align="right"><?php echo $presence_c; ?></td>
											 <td align="right"><?php echo $presence_i; ?></td>
											 <td align="right"><?php echo $presence_a; ?></td>
											 <td align="right"><b><?php echo number_format($presentase,2); ?></b></td>
										</tr>										
									<?php $no++; endforeach; ?>
									</tbody>	
								</table> 								
							</div>
						
							
						</div>
					</div>
				</div><!-- End Panel Body -->
				
				<!-- Panel Footer -->
				<div class="panel-footer hidden">
					<div class="form-group">
						<div class="col-sm-3 ">
							<form method="post" action="<?php echo site_url();?>/report/presence/download">
							<input type="hidden" name="branch" value="<?php echo $branch; ?>" />
							<input type="hidden" name="startdate" value="<?php echo $start_date; ?>" />
							<input type="hidden" name="enddate" value="<?php echo $end_date; ?>" />
							<button type="submit" class="btn btn-primary">Download Report</button>
							</form>
						</div>
					</div>
				</div>
			
	</div>
</div>	
