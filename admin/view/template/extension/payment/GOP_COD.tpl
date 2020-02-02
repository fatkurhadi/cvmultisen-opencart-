<?php
/**
 * @extension-payment	GOP_COD
 * @author-name			Michail Gkasios
 * @copyright			Copyright (C) 2013 GKASIOS
 * @license				GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
?>
<?= $header; ?><?= $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="GOP_COD-form" class="btn btn-primary" data-toggle="tooltip" title="<?= $button_save; ?>"><i class="fa fa-save"></i></button>
				<a class="btn btn-default" data-toggle="tooltip" href="<?= $cancel; ?>" title="<?= $button_cancel; ?>"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?= $heading_title; ?></h1>
			<ul class="breadcrumb">
			<?php
			foreach($breadcrumbs as $breadcrumb)
			{
			?>
				<li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
			<?php
			}
			?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
	<?php
	if($GOP_COD_warning_error)
	{
	?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $GOP_COD_warning_error; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php
	}
	?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs" id="GOP_COD-tabs">
					<li><a style="height: 40px;" href="#tab-default" data-toggle="tab"><?= $text_default; ?></a></li>
					<?php
					foreach($extensions as $extension)
					{
					?>
					<li><a style="height: 40px;" data-toggle="tab" href="#tab-<?= $extension['name']; ?>"><?= $extension['title']; ?></a></li>
					<?php
					}
					?>
					<li><a style="height: 40px; margin: 0px; padding: 5px 5px 0px 5px;" data-toggle="tab" href="#tab-GKASIOS"><img style="width: 32px; height: 32px;" src="<?= $small_logo; ?>" alt="GKASIOS" title="GKASIOS"></a></li>
				</ul>
				<form method="post" enctype="multipart/form-data" action="<?= $action; ?>" class="form-horizontal" id="form-html-content">
					<div class="tab-content">
						<div class="tab-pane" id="tab-default">
							<div class="col-sm-2">
								<ul class="nav nav-pills nav-stacked" id="module">
									<li><a data-toggle="tab" href="#tab-default-extension"><?= $tab_extension; ?></a></li>
									<li><a data-toggle="tab" href="#tab-default-general"><?= $tab_general; ?></a></li>
									<?php
									foreach($geo_zones as $geo_zone)
									{
									?>
									<li><a data-toggle="tab" href="#tab-default-geozone-<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></a></li>
									<?php
									}
									?>
								</ul>
							</div>
							<div class="col-sm-10">
								<div class="tab-content">
									<div class="tab-pane" id="tab-default-extension">
										<div class="form-group">
											<label for="GOP_COD_status" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_extension_status_info; ?>"><?= $entry_extension_status; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_status" class="form-control">
													<option value="0" <?php if($GOP_COD_status == 0){ echo 'selected'; } ?>><?= $text_disabled; ?></option>
													<option value="1" <?php if($GOP_COD_status == 1){ echo 'selected'; } ?>><?= $text_enabled; ?></option>
												</select>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="tab-default-general">
										<div class="form-group">
											<label for="GOP_COD_default_status" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_status_info; ?>"><?= $entry_status; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_default_status" class="form-control">
													<option value="0" <?php if($GOP_COD_default_status == 0){ echo 'selected'; } ?>><?= $text_disabled; ?></option>
													<option value="1" <?php if($GOP_COD_default_status == 1){ echo 'selected'; } ?>><?= $text_enabled; ?></option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="GOP_COD_default_shipping_geo_zone" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_shipping_geo_zone_info; ?>"><?= $entry_shipping_geo_zone; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_default_shipping_geo_zone" class="form-control">
													<option value="0" <?php if($GOP_COD_default_shipping_geo_zone == 0){ echo 'selected'; }?>><?= $text_disabled; ?></option>
													<option value="1" <?php if($GOP_COD_default_shipping_geo_zone == 1){ echo 'selected'; }?>><?= $text_enabled; ?></option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="GOP_COD_default_sort_order" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_sort_order_info; ?>"><?= $entry_sort_order; ?></span></label>
											<div class="col-sm-10">
												<input type="text" name="GOP_COD_default_sort_order" class="form-control" value="<?= $GOP_COD_default_sort_order; ?>" />
												<?php
												if($GOP_COD_default_sort_order_error)
												{
												?>
												<div class="text-danger"><?= $GOP_COD_default_sort_order_error; ?></div>
												<?php
												}
												?>
											</div>
										</div>
									</div>
									<?php
									foreach($geo_zones as $geo_zone)
									{
									?>
									<div class="tab-pane" id="tab-default-geozone-<?= $geo_zone['geo_zone_id']; ?>">
										<div class="form-group" style="background-color: #A5D3EC;">
											<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_customer_group" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_customer_group_info; ?>"><?= $entry_customer_group; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_customer_group" class="form-control" id="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_customer_group" onchange="showCustomerGroupOptions('default', '<?= $geo_zone['geo_zone_id']; ?>')">
												<?php
												foreach($customer_groups as $customer_group)
												{
												?>
													<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
												<?php
												}
												?>
												</select>
											</div>
										</div>
										<?php
										$display = true;
										foreach($customer_groups as $customer_group)
										{
										?>
										<div name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>" id="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>" <?php if($display){ $display = false; }else{ echo 'style="display: none;"'; } ?>>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_tax_class_id" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_tax_class_info; ?>"><?= $entry_tax_class; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_tax_class_id" class="form-control">
														<option value="0" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_tax_class_id'} == 0){ echo 'selected'; } ?>><?= $text_none; ?></option>
														<?php
														foreach($tax_classes as $tax_class)
														{
														?>
														<option value="<?= $tax_class['tax_class_id']; ?>" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_tax_class_id'} == $tax_class['tax_class_id']){ echo 'selected'; } ?>><?= $tax_class['title']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_method_info; ?>"><?= $entry_method; ?></span></label>
												<div class="col-sm-10">
													<input type="radio" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" class="radio-inline" value="0" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 0){ echo 'checked'; } ?> /><?= $entry_flat_rate; ?>
													<input type="radio" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" class="radio-inline" value="1" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 1){ echo 'checked'; } ?> /><?= $entry_percentage; ?>
													<input type="radio" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" class="radio-inline" value="2" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 2){ echo 'checked'; } ?> /><?= $entry_custom; ?>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_flat_rate_info; ?>"><?= $entry_flat_rate; ?></span></label>
												<div class="col-sm-10">
													<input type="text" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat" class="form-control" value="<?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat'} ?>" />
													<?php
													if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_error'})
													{
													?>
													<div class="text-danger"><?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_error'}; ?></div>
													<?php
													}
													?>
													<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat_currency" class="form-control">
													<?php
													foreach($currencies as $currency)
													{
													?>
														<option value="<?= $currency['currency_id']?>" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_currency'} == $currency['currency_id']){ echo 'selected'; }?>><?= $currency['title']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_percent" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_percentage_info; ?>"><?= $entry_percentage; ?></span></label>
												<div class="col-sm-10">
													<input type="text" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_percent" class="form-control" value="<?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent'}; ?>" />
													<?php
													if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent_error'})
													{
													?>
													<div class="text-danger"><?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent_error'}; ?></div>
													<?php
													}
													?>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_custom" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_custom_info; ?>"><?= $entry_custom; ?></span></label>
												<div class="col-sm-10">
													<textarea name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_custom" class="form-control" cols="40" rows="5"><?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_custom'}; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_enable_rule" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_enable_rule_info; ?>"><?= $entry_enable_rule; ?></span></label>
												<div class="col-sm-10">
													<textarea name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_enable_rule" class="form-control" cols="40" rows="5"><?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_enable_rule'}; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_status_id" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_status_info; ?>"><?= $entry_order_status; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_status_id" class="form-control">
													<?php
													foreach($order_statuses as $order_status)
													{
													?>
														<option value="<?= $order_status['order_status_id']; ?>" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_status_id'} == $order_status['order_status_id']){ echo 'selected';} ?>><?= $order_status['name']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_total_info; ?>"><?= $entry_order_total; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total" class="form-control">
														<option value="0" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total'} == 0){ echo 'selected'; }?>><?= $text_disabled; ?></option>
														<option value="1" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total'} == 1){ echo 'selected'; }?>><?= $text_enabled; ?></option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total_sort_order" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_total_sort_order_info; ?>"><?= $entry_order_total_sort_order; ?></span></label>
												<div class="col-sm-10">
													<input type="text" name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total_sort_order" class="form-control" value="<?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order'}; ?>" />
													<?php
													if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order_error'})
													{
													?>
													<div class="text-danger"><?= ${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order_error'}; ?></div>
													<?php
													}
													?>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_status" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_status_info; ?>"><?= $entry_status; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_default_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_status" class="form-control">
														<option value="0" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_status'} == 0){ echo 'selected'; }?>><?= $text_disabled; ?></option>
														<option value="1" <?php if(${'GOP_COD_default_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_status'} == 1){ echo 'selected'; }?>><?= $text_enabled; ?></option>
													</select>
												</div>
											</div>
										</div>
										<?php
										}
										?>
									</div>
									<?php
									}
									?>
								</div>
							</div>
						</div>
						<?php
						foreach($extensions as $extension)
						{
						?>
						<!--Extension <?= $extension['name']; ?> Tab-->
						<div class="tab-pane" id="tab-<?= $extension['name']; ?>">
							<div class="col-sm-2">
								<ul class="nav nav-pills nav-stacked" id="module">
									<li><a data-toggle="tab" href="#tab-<?= $extension['name']; ?>-general"><?= $tab_general; ?></a></li>
									<?php
									foreach($geo_zones as $geo_zone)
									{
									?>
									<li><a data-toggle="tab" href="#tab-<?= $extension['name']; ?>-geozone-<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></a></li>
									<?php
									}
									?>
								</ul>
							</div>
							<div class="col-sm-10">
								<div class="tab-content">
									<div class="tab-pane" id="tab-<?= $extension['name']; ?>-general">
										<div class="form-group">
											<label for="GOP_COD_<?= $extension['name']; ?>_status" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_status_info; ?>"><?= $entry_status; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_<?= $extension['name']; ?>_status" class="form-control">
													<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_status'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
													<option value="1" <?php if(${'GOP_COD_' . $extension['name'] . '_status'} == 1){ echo 'selected'; }?>><?= $text_disabled; ?></option>
													<option value="2" <?php if(${'GOP_COD_' . $extension['name'] . '_status'} == 2){ echo 'selected'; }?>><?= $text_enabled; ?></option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="GOP_COD_<?= $extension['name']; ?>_shipping_geo_zone" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_shipping_geo_zone_info; ?>"><?= $entry_shipping_geo_zone; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_<?= $extension['name']; ?>_shipping_geo_zone" class="form-control">
													<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_shipping_geo_zone'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
													<option value="1" <?php if(${'GOP_COD_' . $extension['name'] . '_shipping_geo_zone'} == 1){ echo 'selected'; }?>><?= $text_disabled; ?></option>
													<option value="2" <?php if(${'GOP_COD_' . $extension['name'] . '_shipping_geo_zone'} == 2){ echo 'selected'; }?>><?= $text_enabled; ?></option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="GOP_COD_<?= $extension['name']; ?>_sort_order" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_sort_order_info; ?>"><?= $entry_sort_order; ?></span></label>
											<div class="col-sm-10">
												<input type="text" name="GOP_COD_<?= $extension['name']; ?>_sort_order" class="form-control" value="<?= ${'GOP_COD_' . $extension['name'] . '_sort_order'}; ?>" />
												<?php
												if(${'GOP_COD_' . $extension['name'] . '_sort_order_error'})
												{
												?>
												<div class="text-danger"><?= ${'GOP_COD_' . $extension['name'] . '_sort_order_error'}; ?></div>
												<?php
												}
												?>
											</div>
										</div>
									</div>
									<?php
									foreach($geo_zones as $geo_zone)
									{
									?>
									<div class="tab-pane" id="tab-<?= $extension['name']; ?>-geozone-<?= $geo_zone['geo_zone_id']; ?>">
										<div class="form-group" style="background-color: #A5D3EC;">
											<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_customer_group" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_customer_group_info; ?>"><?= $entry_customer_group; ?></span></label>
											<div class="col-sm-10">
												<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_customer_group" class="form-control" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_customer_group" onchange="showCustomerGroupOptions('<?= $extension['name']; ?>', '<?= $geo_zone['geo_zone_id']; ?>')">
												<?php
												foreach($customer_groups as $customer_group)
												{
												?>
													<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
												<?php
												}
												?>
												</select>
											</div>
										</div>
										<?php
										$display = true;
										foreach($customer_groups as $customer_group)
										{
										?>
										<div name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>" <?php if($display){ $display = false; }else{ echo 'style="display: none;"'; } ?>>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_tax_class_id" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_tax_class_info; ?>"><?= $entry_tax_class; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_tax_class_id" class="form-control">
														<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_tax_class_id'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
														<option value="-1" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_tax_class_id'} == -1){ echo 'selected'; }?>><?= $text_none; ?></option>
														<?php
														foreach($tax_classes as $tax_class)
														{
														?>
														<option value="<?= $tax_class['tax_class_id']; ?>" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_tax_class_id'} == $tax_class['tax_class_id']){ echo 'selected'; }?>><?= $tax_class['title']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_method_info; ?>"><?= $entry_method; ?></span></label>
												<div class="col-sm-10">
													<input type="radio" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 0){ echo 'checked'; } ?> onClick="showMethodOptions('<?= $extension['name']; ?>', '<?= $geo_zone['geo_zone_id']; ?>', '<?= $customer_group['customer_group_id']; ?>')" ><?= $text_default; ?>
													<input type="radio" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" value="1" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 1){ echo 'checked'; } ?> onClick="showMethodOptions('<?= $extension['name']; ?>', '<?= $geo_zone['geo_zone_id']; ?>', '<?= $customer_group['customer_group_id']; ?>')" ><?= $entry_flat_rate; ?>
													<input type="radio" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" value="2" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 2){ echo 'checked'; } ?> onClick="showMethodOptions('<?= $extension['name']; ?>', '<?= $geo_zone['geo_zone_id']; ?>', '<?= $customer_group['customer_group_id']; ?>')" ><?= $entry_percentage; ?>
													<input type="radio" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method" value="3" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} == 3){ echo 'checked'; } ?> onClick="showMethodOptions('<?= $extension['name']; ?>', '<?= $geo_zone['geo_zone_id']; ?>', '<?= $customer_group['customer_group_id']; ?>')" ><?= $entry_custom; ?>
												</div>
											</div>
											<div name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_0" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} !=0){ echo 'style="display: none;"'; }?>>
											</div>
											<div name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_1" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_1" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} !=1){ echo 'style="display: none;"'; }?>>
												<div class="form-group">
													<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_flat_rate_info; ?>"><?= $entry_flat_rate; ?></span></label>
													<div class="col-sm-10">
														<input type="text" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat" class="form-control" value="<?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat'}; ?>" />
														<?php
														if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_error'})
														{
														?>
														<div class="text-danger"><?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_error'}; ?></div>
														<?php
														}
														?>
														<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_flat_currency" class="form-control">
														<?php
														foreach($currencies as $currency)
														{
														?>
															<option value="<?= $currency['currency_id']?>" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_flat_currency'} == $currency['currency_id']){ echo 'selected'; }?>><?= $currency['title']; ?></option>
														<?php
														}
														?>
														</select>
													</div>
												</div>
											</div>
											<div name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_2" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_2" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} != 2){ echo 'style="display: none;"'; }?>>
												<div class="form-group">
													<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_percent" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_percentage_info; ?>"><?= $entry_percentage; ?></span></label>
													<div class="col-sm-10">
														<input type="text" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_percent" class="form-control" value="<?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent'}; ?>" />
														<?php
														if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent_error'})
														{
														?>
														<div class="text-danger"><?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_percent_error'}; ?></div>
														<?php
														}
														?>
													</div>
												</div>
											</div>
											<div name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_3" id="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_method_3" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_method'} != 3){ echo 'style="display: none;"'; } ?>>
												<div class="form-group">
													<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_custom" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_custom_info; ?>"><?= $entry_custom; ?></span></label>
													<div class="col-sm-10">
														<textarea name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_custom" class="form-control" cols="40" rows="5"><?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_custom'}; ?></textarea>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_enable_rule" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_enable_rule_info; ?>"><?= $entry_enable_rule; ?></span></label>
												<div class="col-sm-10">
													<textarea name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_enable_rule" class="form-control" cols="40" rows="5"><?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_enable_rule'}; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_status_id" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_status_info; ?>"><?= $entry_order_status; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_status_id" class="form-control">
														<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_status_id'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
														<?php
														foreach($order_statuses as $order_status)
														{
														?>
														<option value="<?= $order_status['order_status_id']; ?>" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_status_id'} == $order_status['order_status_id']){ echo 'selected'; } ?>><?= $order_status['name']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_total_info; ?>"><?= $entry_order_total; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total" class="form-control">
														<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
														<option value="1" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total'} == 1){ echo 'selected'; }?>><?= $text_disabled; ?></option>
														<option value="2" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total'} == 2){ echo 'selected'; }?>><?= $text_enabled; ?></option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total_sort_order" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_order_total_sort_order_info; ?>"><?= $entry_order_total_sort_order; ?></span></label>
												<div class="col-sm-10">
													<input type="text" name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_order_total_sort_order" class="form-control" value="<?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order'}; ?>" />
													<?php
													if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order_error'})
													{
													?>
													<div class="text-danger"><?= ${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_order_total_sort_order_error'}; ?></div>
													<?php
													}
													?>
												</div>
											</div>
											<div class="form-group">
												<label for="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_status" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?= $entry_status_info; ?>"><?= $entry_status; ?></span></label>
												<div class="col-sm-10">
													<select name="GOP_COD_<?= $extension['name']; ?>_<?= $geo_zone['geo_zone_id']; ?>_<?= $customer_group['customer_group_id']; ?>_status" class="form-control">
														<option value="0" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_status'} == 0){ echo 'selected'; }?>><?= $text_default; ?></option>
														<option value="1" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_status'} == 1){ echo 'selected'; }?>><?= $text_disabled; ?></option>
														<option value="2" <?php if(${'GOP_COD_' . $extension['name'] . '_' . $geo_zone['geo_zone_id'] . '_' . $customer_group['customer_group_id'] . '_status'} == 2){ echo 'selected'; }?>><?= $text_enabled; ?></option>
													</select>
												</div>
											</div>
										</div>
										<?php
										}
										?>
									</div>
									<?php
									}
									?>
								</div>
							</div>
						</div>
						<?php
						}
						?>
						<div class="tab-pane" id="tab-GKASIOS">
							<div class="form-horizontal" style="padding-left: 15px; padding-right: 15px">
								<div class="form-group">
									<label class="col-sm-2 control-label"><?= $credits; ?></label>
									<div class="col-sm-10"><?= $credits_info; ?></div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?= $license; ?></label>
									<div class="col-sm-10"><?= $license_info; ?></div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?= $donate; ?></label>
									<div class="col-sm-10"><?= $donate_info; ?></div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript"><!--
function showCustomerGroupOptions(extension_name, geo_zone)
{
	var drop_down_group = document.getElementById("GOP_COD_" + extension_name + "_" + geo_zone + "_customer_group");
	for(var i = 0; i < drop_down_group.options.length; i++)
	{
		var drop_down_id = drop_down_group.options[i].value;
		method = document.getElementById("GOP_COD_" + extension_name + "_" + geo_zone + "_" + drop_down_id);
		method.setAttribute('style',method.style.cssText);
		method.style.cssText = 'display:none;';
		if(drop_down_group.options[drop_down_group.selectedIndex].value == drop_down_id)
		{
			method.style.cssText = 'display:block;';
		}
	}
}

function showMethodOptions(extension_name, geo_zone, customer_group)
{
	radio_group = document.getElementsByName("GOP_COD_" + extension_name + "_" + geo_zone + "_" + customer_group + "_method");
	for(var i = 0; i < radio_group.length; i++)
	{
		var button = radio_group[i];
		method = document.getElementById("GOP_COD_" + extension_name + "_" + geo_zone + "_" + customer_group + "_method_" + button.value);
		method.setAttribute('style',method.style.cssText);
		method.style.cssText = 'display:none;';

		if(button.checked)
		{
			method.style.cssText = 'display:block;';
		}
	}
}

function submitPaypal()
{
	var win = window.open();
	var paypalHTML = '<html><body><form method="post" action="https://www.paypal.com/cgi-bin/webscr" id="paypal" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="<?= $paypal_button_id; ?>"></form></body></html>';
	win.document.write(paypalHTML);
	win.document.getElementById("paypal").submit();
}

$('#GOP_COD-tabs li:first-child a').tab('show');
$('#tab-default li:first-child a').tab('show');
//--></script>
<?= $footer; ?>