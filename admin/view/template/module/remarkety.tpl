<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>

            <?php if('register' == $installType): ?>
            <div class="panel-body">
                <div style="margin-bottom: 25px;">
                    <h3><?php echo $text_create_new_remarkety_account;?></h3>
                    <h4><?php echo $text_already_registered_to_remarkety;?> <a href="<?php echo $login_url;?>"><?php echo $text_click_here;?></a></h4>
                </div>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-email"><?php echo $entry_email; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="entry-email" class="form-control"/>
                            <?php if ($error_email) { ?>
                            <div class="text-danger"><?php echo $error_email; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-first_name"><?php echo $entry_first_name; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="firstName" value="<?php echo $firstName; ?>" placeholder="<?php echo $entry_first_name; ?>" id="entry-first_name" class="form-control"/>
                            <?php if ($error_firstName) { ?>
                            <div class="text-danger"><?php echo $error_firstName; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-last_name"><?php echo $entry_last_name; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="lastName" value="<?php echo $lastName; ?>" placeholder="<?php echo $entry_last_name; ?>" id="entry-last_name" class="form-control"/>
                            <?php if ($error_lastName) { ?>
                            <div class="text-danger"><?php echo $error_lastName; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-phone"><?php echo $entry_phone; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="<?php echo $entry_phone; ?>" id="entry-phone" class="form-control"/>
                            <?php if ($error_phone) { ?>
                            <div class="text-danger"><?php echo $error_phone; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-password"><?php echo $entry_password; ?></label>
                        <div class="col-sm-10">
                            <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="entry-password" class="form-control"/>
                            <?php if ($error_password) { ?>
                            <div class="text-danger"><?php echo $error_password; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-store_id"><?php echo $entry_store_id; ?></label>
                        <div class="col-sm-10">
                            <select name="store_id" placeholder="<?php echo $entry_store_id; ?>" id="entry-store_id" class="form-control">
                                <?php foreach($store_list as $key => $store):?>
                                    <?php if ('stores' === $key): ?>
                                        <?php foreach ($store as $myStore):?>
                                            <option value="<?php echo $myStore['store_id'];?>" <?php if($store_id == $myStore['store_id']) echo 'selected="selected"';?>><?php echo $myStore['name'];?></option>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                        <option value="<?php echo $store['store_id'];?>" <?php if($store_id == $store['store_id']) echo 'selected="selected"';?>><?php echo $store['name'];?></option>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </select>
                            <?php if ($error_store_id) { ?>
                            <div class="text-danger"><?php echo $error_store_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="entry_agreement"></label>
                        <div class="col-sm-10">
                            <input type="checkbox" name="remarkety_agreement" value="1" id="entry_agreement" class=""/>
                            <span class=""><b><?php echo $entry_agreement; ?> <a href=""><?php echo $text_terms_of_use;?></a></b></span>
                            <?php if ($error_agreement) { ?>
                            <div class="text-danger"><?php echo $error_agreement; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>

            <?php elseif('login' == $installType):?>
            <div class="panel-body">
                <div style="margin-bottom: 25px;">
                    <h3><?php echo $text_login_to_remarkety;?></h3>
                    <h4><?php echo $text_dont_have_account;?> <a href="<?php echo $register_url;?>"><?php echo $text_click_here;?></a></h4>
                </div>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-email"><?php echo $entry_email; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="entry-email" class="form-control"/>
                            <?php if ($error_email) { ?>
                            <div class="text-danger"><?php echo $error_email; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-password"><?php echo $entry_password; ?></label>
                        <div class="col-sm-10">
                            <input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="entry-password" class="form-control"/>
                            <?php if ($error_password) { ?>
                            <div class="text-danger"><?php echo $error_password; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-store_id"><?php echo $entry_store_id; ?></label>
                        <div class="col-sm-10">
                            <select name="store_id" placeholder="<?php echo $entry_store_id; ?>" id="entry-store_id" class="form-control">
                                <?php foreach($store_list as $key => $store):?>
                                <?php if ('stores' === $key): ?>
                                <?php foreach ($store as $myStore):?>
                                <option value="<?php echo $myStore['store_id'];?>" <?php if($store_id == $myStore['store_id']) echo 'selected="selected"';?>><?php echo $myStore['name'];?></option>
                                <?php endforeach;?>
                                <?php else: ?>
                                <option value="<?php echo $store['store_id'];?>" <?php if($store_id == $store['store_id']) echo 'selected="selected"';?>><?php echo $store['name'];?></option>
                                <?php endif;?>
                                <?php endforeach;?>
                            </select>
                            <?php if ($error_store_id) { ?>
                            <div class="text-danger"><?php echo $error_store_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>

            <?php else:?>
            <div class="panel-body">
                <div style="margin-bottom: 25px;">
                    <h3><?php echo $text_welcome_to_remarkety;?></h3>
                    <p>1. <?php echo $text_sign_in_to_your_account;?> <a href="<?php echo $text_remarkety_login_url;?>" target="_blank"><?php echo $text_here;?></a></p>
                    <p>2. <?php echo $text_create_campaigns_send_emails;?></p>
                    <p>3. <?php echo $text_increase_sales_and_customers;?></p>
                    <p>4. <?php echo $text_meed_help;?>
                        <a href="mailto:<?php echo $text_support_email;?>">
                            <?php echo $text_support_email;?> (<?php echo $text_support_phone;?>)
                        </a>
                    </p>
                </div>
            </div>
            <?php endif;?>

            <div class="panel-body">
                <?php if (!empty($button_request_queue)) :?>
                <div class="pull-left">
                    <a href="<?php echo $queue_url; ?>" data-toggle="tooltip" title="<?php echo $button_request_queue; ?>" class="btn btn-primary"><i class="fa fa-history"></i></a>
                </div>
                <?php endif;?>

                <div class="pull-right">
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
                    <?php if (!empty($uninstall_url)):?>
                    <a href="<?php echo $uninstall_url; ?>" data-toggle="tooltip" title="<?php echo $button_uninstall; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a>
                    <?php endif;?>
                    <?php if(empty($installType)): ?>
                    <a href="<?php echo $reinstall; ?>" data-toggle="tooltip" title="<?php echo $button_reinstall; ?>" class="btn btn-primary"><i class="fa fa-retweet"></i></a>
                    <?php else: ?>
                    <button type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>