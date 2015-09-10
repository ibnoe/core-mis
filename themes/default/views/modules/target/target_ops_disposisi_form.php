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
								<label for="group_name" class="col-sm-3 control-label">Field Officer</label>
								<div class="col-sm-4">
									<select name="target_officer_officer" data-required="true" class="form-control" >
									<?php
									
										echo '<option value="0">Pilih FO untuk diberi target</option>'; 
										foreach ($officer as $o) {
									  		if($form_type == 'edit' && $target[0]['target_officer_officer'] == $o->officer_id)
									  			{ $selected = 'selected="selected"'; }
									  		else { $selected = ''; }
									  		echo '<option value="'.$o->officer_id.'" '.$selected.'>'.$o->officer_name.'</option>';
									  	}	
									  ?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="target_officer_amount" class="col-sm-3 control-label">Nilai Target</label>
								<div class="col-sm-4">
									<input type="text" name="target_officer_amount" class="form-control" id="" placeholder="" value="<?php echo set_value('target_officer_amount', isset($target[0]['target_officer_amount']) ? $target[0]['target_officer_amount'] : ''); ?>" />
								</div>
							</div>

							
							
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Catatan Tentang Target</label>
								<div class="col-sm-4">
									<input type="text" name="target_officer_remarks" class="form-control" id="" placeholder="" value="<?php echo set_value('target_officer_remarks', isset($target[0]['target_officer_remarks']) ? $target[0]['target_officer_remarks'] : ''); ?>" />
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
							<input type="hidden" name="target_officer_id" class="form-control" id="target_officer_id" placeholder="" value="<?php echo set_value('target_officer_id', isset($target[0]['target_officer_id']) ? $target[0]['target_officer_id'] : ''); ?>">
							<button type="submit" class="btn btn-primary">Save Data</button>
						</div>
					</div>
				</div>
			</div>
			
			
			

			
			
			
		</form>
	</div>
</div>	