<section class="main">
	<div class="container">
	
		<div id="module_title">
			<div class="m-b-md"><h3 class="m-b-none"><?php echo $menu_title; ?></h3></div>
		</div>
		<?php  	if($form_type == 'edit') $hidden = array('tid' => $target[0]['target_id'], 'type' => $form_type);
				else $hidden = array('type' => $form_type);
				$attributes = array('class' => 'form-horizontal', 'data-validate' => 'parsley');
          		echo form_open_multipart('', $attributes, $hidden);
        ?>
			<div class="panel panel-default">
			
				<!-- Panel Head -->
				<div class="panel-heading">
					<!-- Nav tabs -->
					<ul class="nav nav-pills">
						<li class="active"><a href="#personalinfo" data-toggle="tab">Target Parameter Info</a></li>
					</ul>
				</div>
				
				<!-- Panel Body -->
				<div class="panel-body">
					<!-- Tab panes -->
					<div class="tab-content">
						<div class="tab-pane active" id="personalinfo">
							<?php echo validation_errors('<div class="alert alert-danger"> <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>', '</div>'); ?>
							 
							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Nama Target *</label>
								<div class="col-sm-4">
									<input type="text" name="target_category" class="form-control" id="group_name" placeholder="" value="<?php echo $target[0]['target_category']; ?>" />
								</div>
							</div>
							
							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Item Target *</label>
								<div class="col-sm-4">
									<select name="target_item" data-required="true" class="form-control" >
									<?php
										echo '<option value="0">Pilih Jenis Target</option>'; 
									  	if($form_type == 'edit')
									  	{ 
									  				if($target[0]['target_item'] == 'PBYN')
									  					echo '<option value="PBYN" selected="selected">Realisasi Pembiayaan</option>';
									  				else if($target[0]['target_item'] == 'OTBJ')
									  					echo '<option value="OTBJ" selected="selected">Outstanding Tabungan Berjangka</option>';
									  				else{
												  		echo '<option value="PBYN">Realisasi Pembiayaan</option>';
												  		echo '<option value="OTBJ">Outstanding Tabungan Berjangka</option>';
									  				}
									  	}else{
									  		echo '<option value="PBYN">Realisasi Pembiayaan</option>';
									  		echo '<option value="OTBJ">Outstanding Tabungan Berjangka</option>';
									  	}
									  ?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Nilai Target</label>
								<div class="col-sm-4">
									<input type="text" name="target_amount" class="form-control" id="group_name" placeholder="" value="<?php echo $target[0]['target_amount']; ?>" />
								</div>
							</div>

							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Cabang</label>
								<div class="col-sm-4">
									<select name="target_branch" data-required="true" class="form-control" >
									<?php
										echo '<option value="0">Pilih Cabang untuk diberi target</option>'; 
										foreach ($branch as $b) {
									  		if($form_type == 'edit' && $target[0]['target_branch'] == $b->branch_id)
									  			{ $selected = 'selected="selected"'; }
									  		else { $selected = ''; }
									  		echo '<option value="'.$b->branch_id.'" '.$selected.'>'.$b->branch_name.'</option>';
									  	}	
									  ?>
									</select>
								</div>
							</div>

							<div class="form-group hidden">
								<label for="group_name" class="col-sm-3 control-label">Officer</label>
								<div class="col-sm-4">
									<select name="target_officer" data-required="true" class="form-control" >
									<?php
										echo '<option value="0">Pilih FO untuk diberi target</option>'; 
										foreach ($officer as $o) {
									  		if($form_type == 'edit' && $target[0]['target_officer'] == $o->officer_id)
									  			{ $selected = 'selected="selected"'; }
									  		else { $selected = ''; }
									  		echo '<option value="'.$o->officer_id.'" '.$selected.'>'.$o->officer_name.'</option>';
									  	}	
									  ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Tanggal Awal Target*</label>
								<div class="col-sm-4">
								  	<input type="text" name="target_startdate" class="datepicker-input form-control" data-date-format="yyyy-mm-dd" value="<?php echo set_value('target_startdate', isset($target[0]['target_startdate']) ? $target[0]['target_startdate'] : date('Y-m-d', strtotime('first day of next month'))); ?>">
								</div>
							</div>
							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Tanggal Akhir Target*</label>
								<div class="col-sm-4">
								  	<input type="text" name="target_enddate" class="datepicker-input form-control" data-date-format="yyyy-mm-dd" value="<?php echo set_value('target_enddate', isset($target[0]['target_enddate']) ? $target[0]['target_enddate'] : date('Y-m-d', strtotime('last day of next month'))); ?>">
								</div>
							</div>

							<div class="form-group">
								<label for="group_name" class="col-sm-3 control-label">Catatan Tentang Target</label>
								<div class="col-sm-4">
									<input type="text" name="target_remarks" class="form-control" id="group_name" placeholder="" value="<?php echo set_value('target_remarks', isset($target[0]['target_remarks']) ? $target[0]['target_remarks'] : ''); ?>" />
								</div>
							</div>
								
						</div>		
						
						
					</div>
					
				</div>
				
				<!-- Panel Footer -->
				<div class="panel-footer">
					<div class="form-group">
						<div class="col-sm-3 ">
							<input type="hidden" name="target_id" class="form-control" id="target_id" placeholder="" value="<?php echo set_value('target_id', isset($target[0]['target_id']) ? $target[0]['target_id'] : ''); ?>">
							<button type="submit" class="btn btn-primary">Save Data</button>
						</div>
					</div>
				</div>
			</div>
			
			
			

			
			
			
		</form>
	</div>
</div>	