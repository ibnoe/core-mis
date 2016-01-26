<?php $client_account =  $this->uri->segment(3); ?>
<section class="main">
	<div class="container">
	
		<div id="module_title">
			<div class="m-b-md"><h3 class="m-b-none"><?php echo $menu_title; ?></h3></div>
		</div>
	
		<form class="form-horizontal" enctype="multipart/form-data" id="" action="" method="post">
			<div class="panel panel-default">
			
				<!-- Panel Head -->
				<div class="panel-heading">
					<!-- Nav tabs -->
					<ul class="nav nav-pills">
						<li class="active"><a href="#personalinfo" data-toggle="tab">Tabungan Sipeci</a></li>
					</ul>
				</div>
				
				<!-- Panel Body -->
				<div class="panel-body">
					<!-- Tab panes -->
					<div class="tab-content">
						<div class="tab-pane active" id="personalinfo">
							<?php echo validation_errors('<div class="alert alert-danger"> <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>', '</div>'); ?>
							
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nomor Rekening</label>
								<div class="col-sm-4">
									<input type="text" name="tabsipeci_account" class="form-control" id="" placeholder="" value="<?php echo  $client_account; ?>" readonly />
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tanggal</label>
								<div class="col-sm-4">
									<?php if(!$data->tabsipeci_date){ ?>
									<input type="text" name="tabsipeci_date" class="form-control datepicker-input" data-date-format="yyyy-mm-dd" id="" placeholder="" value="<?php echo date('Y-m-d'); ?>">
									<?php }else{ ?>
									<input type="text" name="tabsipeci_date" class="form-control datepicker-input" data-date-format="yyyy-mm-dd" id="" placeholder="" value="<?php echo set_value('tabsipeci_tr_date', isset($data->tabsipeci_tr_date) ? $data->tabsipeci_date : ''); ?>">
									<?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Debet</label>
								<div class="col-sm-4">
									<input type="text" name="tabsipeci_debet" class="form-control" id="" placeholder="" value="<?php echo set_value('tabsipeci_debet', isset($data->tabsipeci_tr_debet) ? $data->tabsipeci_tr_debet : '0'); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kredit</label>
								<div class="col-sm-4">
									<input type="text" name="tabsipeci_credit" class="form-control" id="" placeholder="" value="<?php echo set_value('tabsipeci_credit', isset($data->tabsipeci_tr_credit) ? $data->tabsipeci_tr_credit : '0'); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Remark</label>
								<div class="col-sm-4">
									<input type="text" name="tabsipeci_remark" class="form-control" id="" placeholder="" value="<?php echo set_value('tabsipeci_remark', isset($data->tabsipeci_tr_remark) ? $data->tabsipeci_tr_remark : ''); ?>" />
								</div>
							</div>
							
							
						</div>		
						
						
					</div>
					
				</div>
				
				<!-- Panel Footer -->
				<div class="panel-footer">
					<div class="form-group">
						<div class="col-sm-3 ">
							<input type="hidden" name="tabsipeci_client" class="form-control" id="tabsipeci_client" placeholder="" value="<?php echo set_value('tabsipeci_client', isset($data->tabsipeci_client) ? $data->tabsipeci_client : ''); ?>">
							<input type="hidden" name="tabsipeci_saldo" class="form-control" id="tabsipeci_saldo" placeholder="" value="<?php echo set_value('tabsipeci_saldo', isset($data->tabsipeci_saldo) ? $data->tabsipeci_saldo : ''); ?>">
							<input type="hidden" name="tabsipeci_total_debet" class="form-control" id="tabsipeci_total_debet" placeholder="" value="<?php echo set_value('tabsipeci_total_debet', isset($data->tabsipeci_debet) ? $data->tabsipeci_debet : ''); ?>">
							<input type="hidden" name="tabsipeci_total_credit" class="form-control" id="tabsipeci_total_credit" placeholder="" value="<?php echo set_value('tabsipeci_total_credit', isset($data->tabsipeci_credit) ? $data->tabsipeci_credit: ''); ?>">
							<button type="submit" class="btn btn-primary">Save Data</button>
						</div>
					</div>
				</div>
			</div>
			
			
			

			
			
			
		</form>
	</div>
</div>	