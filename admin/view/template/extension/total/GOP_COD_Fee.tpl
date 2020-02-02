<?php
/**
 * @extension-total	GOP_COD_Fee
 * @author-name		Michail Gkasios
 * @copyright		Copyright (C) 2013 GKASIOS
 * @license			GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
?>
<?= $header; ?><?= $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="<?= $extension_name; ?>-form" class="btn btn-primary" data-toggle="tooltip" title="<?= $button_save; ?>"><i class="fa fa-save"></i></button>
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
	if($GOP_COD_Fee_warning_error)
	{
	?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $GOP_COD_Fee_warning_error; ?>
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
				<ul class="nav nav-tabs" id="<?= $extension_name; ?>-tabs">
					<li><a style="height: 40px;" data-toggle="tab" href="#tab-configuration"><i class="fa fa-2x fa-gears"></i></a></li>
					<li><a style="height: 40px; margin: 0px; padding: 5px 5px 0px 5px;" data-toggle="tab" href="#tab-GKASIOS"><img style="width: 32px; height: 32px;" src="<?= $small_logo; ?>" alt="GKASIOS" title="GKASIOS"></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane" id="tab-configuration">
						<form method="post" enctype="multipart/form-data" action="<?= $action; ?>" class="form-horizontal" id="form-<?= $extension_name; ?>">
							<div class="form-group">
								<label for="<?= $extension_name; ?>_status" class="col-sm-2 control-label"><?= $entry_status; ?></label>
								<div class="col-sm-10">
									<select name="<?= $extension_name; ?>_status" class="form-control" id="<?= $extension_name; ?>_status">>
										<option value="1" <?php if($GOP_COD_Fee_status == 1){ echo 'selected'; } ?>><?= $text_enabled; ?></option>
										<option value="0" <?php if($GOP_COD_Fee_status == 0){ echo 'selected'; } ?>><?= $text_disabled; ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="<?= $extension_name; ?>_sort_order" class="col-sm-2 control-label"><?= $entry_sort_order; ?></label>
								<div class="col-sm-10">
									<input type="text" name="<?= $extension_name; ?>_sort_order" class="form-control" value="<?= $GOP_COD_Fee_sort_order; ?>" />
									<?php
									if($GOP_COD_Fee_sort_order_error)
									{
									?>
									<div class="text-danger"><?= $GOP_COD_Fee_sort_order_error; ?></div>
									<?php
									}
									?>
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane" id="tab-GKASIOS">
						<div class="form-horizontal">
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
			</div>
		</div>
	</div>
</div>

<script type="text/javascript"><!--
function submitPaypal()
{
	var win = window.open();
	var paypalHTML = '<html><body><form method="post" action="https://www.paypal.com/cgi-bin/webscr" id="paypal" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="<?= $paypal_button_id; ?>"></form></body></html>';
	win.document.write(paypalHTML);
	win.document.getElementById("paypal").submit();
}

$('#GOP_COD_Fee-tabs li:first-child a').tab('show');
$('#tab-configuration li:first-child a').tab('show');
//--></script>
<?= $footer; ?>