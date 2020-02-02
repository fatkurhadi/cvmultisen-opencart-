<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $queue_heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if (isset($error_warning) && $error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $queue_config_title; ?></h3>
            </div>

            <div class="panel-body">
                <form action="" method="post" enctype="multipart/form-data" id="form-remarkety-config" class="form-horizontal">
                    <div style="padding-bottom: 5px; clear: both;"><div class="col-sm-2"></div><div class="col-sm-10"><?php echo $queue_config_intervals_description;?></div><div class="clearfix"></div> </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-intervals"><?php echo $entry_intervals; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="intervals" value="<?php echo $intervals; ?>" placeholder="<?php echo $entry_intervals; ?>" id="entry-intervals" class="form-control"/>
                            <?php if ($error_intervals) { ?>
                            <div class="text-danger"><?php echo $error_intervals; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="pull-right">
                        <button type="submit" form="form-remarkety-config" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $queue_text_list; ?></h3>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-left">
                                <a href="<?php echo $sort_queue_id; ?>" class="<?php if ($sort == 'queue_id'){ echo strtolower($order);} ?>"><?php echo $column_name_queue_id; ?></a>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo $sort_event_type; ?>" class="<?php if ($sort == 'event_type'){ echo strtolower($order);} ?>"><?php echo $column_name_event_type; ?></a>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo $sort_status; ?>" class="<?php if ($sort == 'status'){ echo strtolower($order);} ?>"><?php echo $column_name_status; ?></a>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo $sort_attempts; ?>" class="<?php if ($sort == 'attempts'){ echo strtolower($order);} ?>"><?php echo $column_name_attempts; ?></a>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo $sort_last_attempt; ?>" class="<?php if ($sort == 'last_attempt'){ echo strtolower($order);} ?>"><?php echo $column_name_last_attempts; ?></a>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo $sort_next_attempt; ?>" class="<?php if ($sort == 'next_attempt'){ echo strtolower($order);} ?>"><?php echo $column_name_next_attempts; ?></a>
                            </td>
                            <td class="text-right"><?php echo $column_action; ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($queue_rows) { ?>
                        <?php foreach ($queue_rows as $row) { ?>
                        <tr>
                             <td class="text-left"><?php echo $row['queue_id']; ?></td>
                             <td class="text-left"><?php echo $row['event_type']; ?></td>
                             <td class="text-left">
                                 <?php if ( 0 == $row['status']): ?>
                                    Failed
                                 <?php else :?>
                                    Pending
                                 <?php endif;?>
                             </td>
                             <td class="text-left"><?php echo $row['attempts']; ?></td>
                             <td class="text-left"><?php echo $row['last_attempt']; ?></td>
                             <td class="text-left"><?php echo $row['next_attempt']; ?></td>
                            <td class="text-right">
                                <a href="<?php echo $delete_link . $row['queue_id']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                                <a href="<?php echo $resend_link . $row['queue_id']; ?>" data-toggle="tooltip" title="<?php echo $button_resend; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } else { ?>
                        <tr>
                            <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>