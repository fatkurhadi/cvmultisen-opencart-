<?php
/**
 * @extension-payment	GOP_COD
 * @author-name			Michail Gkasios
 * @copyright			Copyright (C) 2013 GKASIOS
 * @license				GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
?>
<div class="buttons">
	<div class="pull-right">
		<input type="button" class="btn btn-primary" id="button-confirm" data-loading-text="<?= $text_loading; ?>" value="<?= $button_confirm; ?>"/>
	</div>
</div>

<script type="text/javascript"><!--
$('#button-confirm').on('click',	function()
									{
										$.ajax(	{
													type: 'get',
													url: 'index.php?route=extension/payment/GOP_COD/confirm',
													cache: false,
													beforeSend: function()
													{
														$('#button-confirm').button('loading');
													},
													complete: function()
													{
														$('#button-confirm').button('reset');
													},
													success: function()
													{
														location = '<?= $continue; ?>';
													}
												});
									});
//--></script>