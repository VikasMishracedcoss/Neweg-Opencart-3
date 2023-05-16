<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a onclick="$('#form').submit();" class="btn btn-primary" title="<?php echo $button_save; ?>"><i
                            class="fa fa-save"></i></a>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
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
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" id="form" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#profileInfo" data-toggle="tab" aria-expanded="false">
                                <i class="fa fa-file-code-o"></i> General
                            </a></li>
                        <li><a href="#profileCategory" data-toggle="tab">
                                <i class="fa fa-gears"></i> Category Mapping
                            </a></li>
                        <li><a href="#profileAttributes" data-toggle="tab">
                                <i class="fa fa-wrench"></i> Attributes
                            </a></li>
                        <li><a href="#profileEbaySettings" data-toggle="tab">
                                <i class="fa fa-info-circle"></i> Ebay Listing
                            </a></li>
                        <li><a href="#profileEbayPayment" data-toggle="tab">
                                <i class="fa fa-money"></i>  Payment
                            </a></li>
                        <li><a href="#profileEbayReturn" data-toggle="tab">
                                <i class="fa fa-fast-backward"></i>  Return
                            </a></li>
                        <li><a href="#profileEbayShipping" data-toggle="tab">
                                <i class="fa fa-truck"></i>  Shipping
                            </a></li>

                    </ul>
                    <input type="hidden" name="id" id="input-id" value="<?php echo $id;?>"/>
                    <div class="tab-content">
                        <div class="tab-pane active" id="profileInfo">

                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $entry_profile_name; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="profile_name" id="input-title" value="<?php echo $profile_name;?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $column_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="status" id="input-status" class="form-control">
                                        <?php if ($status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $entry_description_template; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[description_template]"   class="form-control">
                                        <option value=""></option>
                                        <?php if($description_templates && count($description_templates)) {
            foreach ($description_templates as $desc_template) { ?>
                                        <option value="<?php echo $desc_template['id'] ?>"
                                        <?php if(isset($profile_specifics['description_template']) && $profile_specifics['description_template'] == $desc_template['id']){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        >
                                        <?php echo $desc_template['template_name']; ?></option>
                                        <?php } ?>     <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-manufacturer"><?php echo $entry_manufacturer; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="manufacturer" value="" id="input-manufacturer"
                                           class="form-control"/>

                                    <div id="product_manufacturer" class="well well-sm"
                                         style="height: 150px; overflow: auto;">
                                        <?php foreach ($product_manufacturers as $product_manufacturer) { ?>
                                        <div id="product_manufacturer<?php echo $product_manufacturer['manufacturer_id']; ?>">
                                            <i class="fa fa-minus-circle"></i> <?php echo $product_manufacturer['name']; ?>
                                            <input type="hidden" name="product_manufacturer[]"
                                                   value="<?php echo $product_manufacturer['manufacturer_id']; ?>"/>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="profileCategory">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td style="text-align: center">
                                        <label for="input-oc-categories"><?php echo $entry_category; ?></label>
                                    </td>
                                    <td style="text-align: center">
                                        <label for="input-ebay-categories">Ebay Categories</label>
                                    </td>
                                    <td style="text-align: center">
                                        <label for="input-store-categories">Ebay Store Category</label>
                                    </td>
                                </tr>
                                </thead>
                                <tbody id="profile_category_mapping">
                                <tr>
                                    <td>
                                        <input type="text" name="category" value="" id="input-category"
                                               class="form-control"/>
                                        <div id="product-category" class="well well-sm"
                                             style="height: 150px; overflow: auto;">
                                            <?php foreach ($product_categories as $product_category) { ?>
                                            <div id="product-category<?php echo $product_category['category_id']; ?>">
                                                <i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                                                <input type="hidden" name="product_category[]"
                                                       value="<?php echo $product_category['category_id']; ?>"/>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="ebay-category">

                                        </div>
                                    </td>
                                    <td style="vertical-align: top">
                                        <div id="ebay_store_category1_section">
                                            <select class="form-control" id="ebay_store_category1" name="profile_specifics[ebay_store_category1]">
                                                <option selected="selected" value="">-</option>
                                                <?php if($store_categories && count($store_categories)) {
                                                foreach ($store_categories as $scat) { ?>
                                                <option value="<?php echo $scat['value'] ?>"
                                                <?php if(isset($profile_specifics['ebay_store_category1']) && $profile_specifics['ebay_store_category1'] == $scat['value']){ ?>
                                                selected="selected"
                                                <?php } ?>
                                                >
                                                <?php echo $scat['name']; ?></option>
                                                <?php } ?>     <?php } ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>



                        </div>
                        <div class="tab-pane" id="profileAttributes">
                            <div id="profile_attribute_mapping_section">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr style="">
                                        <td colspan="3">
                                            <div style="font-size: 16px; text-align: center; padding: 5px;">Ebay
                                                Required/Optional Attributes Mapping
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">
                                            <label for="input-ebay-attributes">Ebay Attributes</label>
                                        </td>
                                        <td style="text-align: center">
                                            <label for="input-store-attributes">Store Attributes</label>
                                        </td>
                                        <td style="text-align: center">
                                            <label for="input-default-values">Set Default Value</label>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody id="profile_attribute_mapping_section">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="profileEbaySettings">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_listing_type; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[listing_type]"   class="form-control">
                                        <?php if($listing_type && count($listing_type)) {
            foreach ($listing_type as $type) { ?>
                                        <option value="<?php echo $type['value'] ?>"
                                        <?php if(isset($profile_specifics['listing_type']) && $profile_specifics['listing_type'] == $type['value']){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        >
                                        <?php echo $type['label']; ?></option>
                                        <?php } ?>     <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_item_condition; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[item_condition]"   class="form-control">
                                        <?php if($item_conditions && count($item_conditions)) {
            foreach ($item_conditions as $condition) { ?>
                                        <option value="<?php echo $condition['ID'] ?>"
                                        <?php if(isset($profile_specifics['item_condition']) && $profile_specifics['item_condition'] == $condition['ID']){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        >
                                        <?php echo $condition['DisplayName']; ?></option>
                                        <?php } ?>     <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_listing_duration; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[listing_duration]"   class="form-control">
                                        <?php if($listing_durations && count($listing_durations)) {
            foreach ($listing_durations as $duration) { ?>
                                        <option value="<?php echo $duration['value'] ?>"
                                        <?php if(isset($profile_specifics['listing_duration']) && $profile_specifics['listing_duration'] == $duration['value']){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        >
                                        <?php echo $duration['label']; ?></option>
                                        <?php } ?>     <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_item_location; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="profile_specifics[item_location]" id="" value="<?php if(isset($profile_specifics['item_location'])){ echo $profile_specifics['item_location']; }?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_postal_code; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="profile_specifics[postal_code]" id="" value="<?php if(isset($profile_specifics['postal_code'])){ echo $profile_specifics['postal_code']; }?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_max_dispatch_time; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="profile_specifics[max_dispatch_time]" id="" value="<?php if(isset($profile_specifics['max_dispatch_time'])){ echo $profile_specifics['max_dispatch_time']; } ?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_max_quantity; ?></label>
                                <div class="col-sm-10">
                                    <input type="number" name="profile_specifics[max_quantity]" id="" value="<?php if(isset($profile_specifics['max_quantity'])){ echo $profile_specifics['max_quantity'];} ?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-variation_type"><?php echo $label_price_type; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[price_variation_type]" onchange="changePriceVariation()" id="price_variation_type" class="form-control">
                                        <option value="default" selected="selected">-None-</option>
                                        <option value="increase_fixed"
                                        <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'increase_fixed'){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        ><?php echo $label_increase_fixed; ?></option>
                                        <option value="increase_percentage"
                                        <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'increase_percentage'){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        ><?php echo $label_increase_percentage; ?></option>
                                        <option value="decrease_fixed"
                                        <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'decrease_fixed'){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        ><?php echo $label_decrease_fixed; ?></option>
                                        <option value="decrease_percentage"
                                        <?php if(isset($profile_specifics['price_variation_type']) && $profile_specifics['price_variation_type'] == 'decrease_percentage'){ ?>
                                        selected="selected"
                                        <?php } ?>
                                        ><?php echo $label_decrease_percentage; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="price_variation_value_section">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_price_variation_value; ?></label>
                                <div class="col-sm-10">
                                    <input type="number" name="profile_specifics[price_variation_value]" id="price_variation_value" value="<?php if(isset($profile_specifics['price_variation_value'])){ echo $profile_specifics['price_variation_value'];} ?>"
                                           class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="profileEbayPayment">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_use_payment_policy; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[use_payment_policy]" id="use_payment_policy" class="form-control" onchange="usePaymentPolicy()">
                                        <?php if (isset($profile_specifics['use_payment_policy']) && $profile_specifics['use_payment_policy']) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="payment_policy">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_payment_policy; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[payment_profile_id]"   class="form-control">
                                            <?php if($payment_policies && count($payment_policies)) {
            foreach ($payment_policies as $type) { ?>
                                            <option value="<?php echo $type['profileId'] ?>"
                                            <?php if(isset($profile_specifics['payment_profile_id']) && $profile_specifics['payment_profile_id'] == $type['profileId']){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $type['profileName']; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="payment_details">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_payment_methods; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[payment_methods][]"  multiple="multiple" size="6" class="form-control">
                                            <?php if($payment_methods && count($payment_methods)) {
            foreach ($payment_methods as $method) { ?>
                                            <option value="<?php echo $method ?>"
                                            <?php if(isset($profile_specifics['payment_methods']) && in_array($method, $profile_specifics['payment_methods'])){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $method; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_paypal_email; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="profile_specifics[paypal_email]" id="" value="<?php if(isset($profile_specifics['paypal_email'])){ echo $profile_specifics['paypal_email']; }?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label" for="input-cod_cost">
        <span data-toggle="tooltip" title="" data-original-title="Applicable only if COD payment method is selected">
            COD Cost</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="number" name="profile_specifics[cod_cost]" id="input-cod_cost" value="<?php if(isset($profile_specifics['cod_cost'])){ echo $profile_specifics['cod_cost']; }?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="profileEbayReturn">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_use_return_policy; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[use_return_policy]" id="use_return_policy" class="form-control" onchange="useReturnPolicy()">
                                        <?php if (isset($profile_specifics['use_return_policy']) && $profile_specifics['use_return_policy']) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="return_policy">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_return_policy; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[return_profile_id]"   class="form-control">
                                            <?php if($return_policies && count($return_policies)) {
            foreach ($return_policies as $type) { ?>
                                            <option value="<?php echo $type['profileId'] ?>"
                                            <?php if(isset($profile_specifics['return_profile_id']) && $profile_specifics['return_profile_id'] == $type['profileId']){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $type['profileName']; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="return_details">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_return_accepted; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[return_accepted]"   class="form-control">
                                            <?php if($return_accepted && count($return_accepted)) {
            foreach ($return_accepted as $key => $ret_acc) { ?>
                                            <option value="<?php echo $key ?>"
                                            <?php if(isset($profile_specifics['return_accepted']) && $profile_specifics['return_accepted'] == $key){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $ret_acc; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_refund_option; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[refund_option]"   class="form-control">
                                            <?php if($refund_options && count($refund_options)) {
            foreach ($refund_options as $key => $ret_acc) { ?>
                                            <option value="<?php echo $key ?>"
                                            <?php if(isset($profile_specifics['refund_option']) && $profile_specifics['refund_option'] == $key){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $ret_acc; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_return_within; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[return_within]"   class="form-control">
                                            <?php if($return_within && count($return_within)) {
            foreach ($return_within as $key => $ret_acc) { ?>
                                            <option value="<?php echo $key ?>"
                                            <?php if(isset($profile_specifics['return_within']) && $profile_specifics['return_within'] == $key){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $ret_acc; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_shipping_cost_paidby; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[shipping_cost_paidby]"   class="form-control">
                                            <?php if($shipping_cost_paidby && count($shipping_cost_paidby)) {
            foreach ($shipping_cost_paidby as $key => $ret_acc) { ?>
                                            <option value="<?php echo $key ?>"
                                            <?php if(isset($profile_specifics['shipping_cost_paidby']) && $profile_specifics['shipping_cost_paidby'] == $key){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $ret_acc; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane" id="profileEbayShipping">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $label_use_shipping_policy; ?></label>
                                <div class="col-sm-10">
                                    <select name="profile_specifics[use_shipping_policy]" id="use_shipping_policy" class="form-control" onchange="useShippingPolicy()">
                                        <?php if (isset($profile_specifics['use_shipping_policy']) && $profile_specifics['use_shipping_policy']) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="shipping_policy">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_shipping_policy; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[shipping_profile_id]"   class="form-control">
                                            <?php if($shipping_policies && count($shipping_policies)) {
            foreach ($shipping_policies as $type) { ?>
                                            <option value="<?php echo $type['profileId'] ?>"
                                            <?php if(isset($profile_specifics['shipping_profile_id']) && $profile_specifics['shipping_profile_id'] == $type['profileId']){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $type['profileName']; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="shipping_details">
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_shipping_service_type; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[shipping_service_type]"   class="form-control">
                                            <?php if($shipping_service_type && count($shipping_service_type)) {
            foreach ($shipping_service_type as $type) { ?>
                                            <option value="<?php echo $type['value'] ?>"
                                            <?php if(isset($profile_specifics['shipping_service_type']) && $profile_specifics['shipping_service_type'] == $type['value']){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $type['label']; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label"
                                           for="input-title"><?php echo $label_free_shipping; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[free_shipping]"   class="form-control">
                                            <?php if($free_shipping && count($free_shipping)) {
            foreach ($free_shipping as $key => $ret_acc) { ?>
                                            <option value="<?php echo $key ?>"
                                            <?php if(isset($profile_specifics['free_shipping']) && $profile_specifics['free_shipping'] == $key){ ?>
                                            selected="selected"
                                            <?php } ?>
                                            >
                                            <?php echo $ret_acc; ?></option>
                                            <?php } ?>     <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $label_domestic_shipping; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[enable_domestic_shipping]" id="enable_domestic_shipping" class="form-control" onchange="enableDomesticShipping()">
                                            <?php if (isset($profile_specifics['enable_domestic_shipping']) && $profile_specifics['enable_domestic_shipping']) { ?>
                                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                            <option value="0"><?php echo $text_disabled; ?></option>
                                            <?php } else { ?>
                                            <option value="1"><?php echo $text_enabled; ?></option>
                                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="domestic_shipping_container">
                                    <div class="bootstrap">
                                        <div class="alert alert-info" >
                                            <span id="">Add Upto 4 Domestic Shipping Rules</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <table class="table table-bordered" id="domestic_shipping">
                                            <thead>
                                            <tr class="headings">
                                                <th><?php echo $label_shipping_service; ?></th>
                                                <th><?php echo $label_shipping_cost; ?></th>
                                                <th><?php echo $label_additional_cost; ?></th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(isset($profile_specifics['domestic_shipping']) && count($profile_specifics['domestic_shipping'])) {
             foreach($profile_specifics['domestic_shipping'] as $key => $dom_shipping){ ?>
                                            <tr>
                                                <td>
                                                    <select class="form-control" name="profile_specifics[domestic_shipping][<?php echo $key ?>][shipping_service]">
                                                        <?php foreach($domestic_shipping_services as $dom_service) { ?>
                                                        <option
                                                        <?php if($dom_shipping['shipping_service'] == $dom_service['value']){ ?>
                                                        selected="selected"
                                                        <?php } ?>
                                                        value="<?php echo $dom_service['value']; ?>"><?php echo $dom_service['label']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control" id="shipping_cost_<?php echo $$key ?>"
                                                           value="<?php if(isset($dom_shipping['shipping_cost'])){ echo $dom_shipping['shipping_cost']; }?>"
                                                           placeholder="<?php echo $label_shipping_cost ?>"
                                                           onkeypress="numberValidate(event, this)"
                                                           name="profile_specifics[domestic_shipping][<?php echo $key ?>][shipping_cost]"
                                                           style="width:50%;"

                                                    />
                                                </td>
                                                <td>
                                                    <input class="form-control" id="additional_cost_<?php echo $$key ?>"
                                                           value="<?php if(isset($dom_shipping['additional_cost'])){ echo $dom_shipping['additional_cost']; }?>"
                                                           placeholder="<?php echo $label_additional_cost ?>"
                                                           onkeypress="numberValidate(event, this)"
                                                           name="profile_specifics[domestic_shipping][<?php echo $key ?>][additional_cost]"
                                                           style="width:50%;"

                                                    />
                                                </td>
                                                <td>
                                                    <button class='btn btn-danger' onclick='deleteRow(this)'>
                                                        <i class='fa fa-eraser'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php }
            }?>
                                            </tbody>
                                        </table>
                                        <table>
                                            <tr>
                                                <td colspan="3" class="text-right">
                                                    <button id="addToEndBtn" class="btn btn-primary" type="button" onclick=
                                                    "addDomShippingRule()" style="">
                                                        <i class="icon-plus-sign-alt"></i>
                                                        <span>Add Rule</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $label_int_shipping; ?></label>
                                    <div class="col-sm-10">
                                        <select name="profile_specifics[enable_international_shipping]" id="enable_international_shipping" class="form-control" onchange="enableIntShipping()">
                                            <?php if (isset($profile_specifics['enable_international_shipping']) && $profile_specifics['enable_international_shipping']) { ?>
                                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                            <option value="0"><?php echo $text_disabled; ?></option>
                                            <?php } else { ?>
                                            <option value="1"><?php echo $text_enabled; ?></option>
                                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="international_shipping_container">
                                    <div class="bootstrap">
                                        <div class="alert alert-info" >
                                            <span id="">Add Upto 5 International Shipping Rules</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <table class="table table-bordered" id="international_shipping">
                                            <thead>
                                            <tr class="headings">
                                                <th><?php echo $label_shipping_service; ?></th>
                                                <th><?php echo $label_shipping_cost; ?></th>
                                                <th><?php echo $label_additional_cost; ?></th>
                                                <th>Locations</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(isset($profile_specifics['international_shipping']) && count($profile_specifics['international_shipping'])) {
             foreach($profile_specifics['international_shipping'] as $key => $int_shipping){ ?>
                                            <tr>
                                                <td>
                                                    <select class="form-control" name="profile_specifics[international_shipping][<?php echo $key ?>][shipping_service]">
                                                        <?php foreach($international_shipping_services as $int_service) { ?>
                                                        <option
                                                        <?php if($int_shipping['shipping_service'] == $int_service['value']){ ?>
                                                        selected="selected"
                                                        <?php } ?>
                                                        value="<?php echo $int_service['value']; ?>"><?php echo $int_service['label']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control" id="shipping_cost_<?php echo $$key ?>"
                                                           value="<?php if(isset($int_shipping['shipping_cost'])){ echo $int_shipping['shipping_cost']; }?>"
                                                           placeholder="<?php echo $label_shipping_cost ?>"
                                                           onkeypress="numberValidate(event, this)"
                                                           name="profile_specifics[international_shipping][<?php echo $key ?>][shipping_cost]"
                                                           style="width:50%;"

                                                    />
                                                </td>
                                                <td>
                                                    <input class="form-control" id="additional_cost_<?php echo $$key ?>"
                                                           value="<?php if(isset($int_shipping['additional_cost'])){ echo $int_shipping['additional_cost']; }?>"
                                                           placeholder="<?php echo $label_additional_cost ?>"
                                                           onkeypress="numberValidate(event, this)"
                                                           name="profile_specifics[international_shipping][<?php echo $key ?>][additional_cost]"
                                                           style="width:50%;"

                                                    />
                                                </td>
                                                <td>
                                                    <select multiple="multiple" class="form-control" name="profile_specifics[international_shipping][<?php echo $key ?>][ship_to_locations][]">
                                                        <?php foreach($ship_to_locations as $loc) { ?>
                                                        <option
                                                        <?php if(isset($int_shipping['ship_to_locations']) && in_array($loc['value'], $int_shipping['ship_to_locations'])){ ?>
                                                        selected="selected"
                                                        <?php } ?>
                                                        value="<?php echo $loc['value']; ?>"><?php echo $loc['label']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class='btn btn-danger' onclick='deleteRow(this)'>
                                                        <i class='fa fa-eraser'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php }
            }?>
                                            </tbody>
                                        </table>
                                        <table>
                                            <tr>
                                                <td colspan="3" class="text-right">
                                                    <button id="addToEndBtn" class="btn btn-primary" type="button" onclick=
                                                    "addIntShippingRule()" style="">
                                                        <i class="icon-plus-sign-alt"></i>
                                                        <span>Add Rule</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="overlay">
    <div id="text"> LOADING.....</div>
</div>


<script>
    var ebay_attributes = [];
    getEbayCategory(0, 1, 0);
    enableDomesticShipping();
    enableIntShipping();
    usePaymentPolicy();
    useReturnPolicy();
    useShippingPolicy();
    changePriceVariation();

    function usePaymentPolicy() {
        var psp = $("#use_payment_policy").val();
        if(psp == 1) {
            $("#payment_details").hide();
            $("#payment_policy").show();
        } else {
            $("#payment_details").show();
            $("#payment_policy").hide();
        }
    }
    function useReturnPolicy() {
        var rsp = $("#use_return_policy").val();
        if(rsp == 1) {
            $("#return_details").hide();
            $("#return_policy").show();
        } else {
            $("#return_details").show();
            $("#return_policy").hide();
        }
    }
    function useShippingPolicy() {
        var usp = $("#use_shipping_policy").val();
        if(usp == 1) {
            $("#shipping_details").hide();
            $("#shipping_policy").show();
        } else {
            $("#shipping_details").show();
            $("#shipping_policy").hide();
        }
    }

    function getEbayCategory(value, current_level, isLeaf) {
        var url = 'index.php?route=cedebay/profile/getCategory&token=<?php echo $token; ?>';
        if (current_level > 1 && !value) {
            document.getElementById("ebay_category_" + current_level).value = value;
            var children = document.getElementById('ebay-category');
            var elementlengh = children.childElementCount;
            for (var i = current_level; i <= elementlengh; i++) {
                document.getElementById("select-container-level-" + i).remove();
            }
            return false;
        }
        $('#ebay-overlay').show();
        var query = $.ajax({
            type: 'POST',
            url: url,
            data: {
                ebay_profile_id: $("input[name=id]").val(),
                category_id: value,
                is_leaf: isLeaf,
                level: current_level
            },
            success: function (res) {
                // try {
                var data = JSON.parse(res);
                $('#ebay-overlay').hide();
                if (current_level == 1 && data.success == false && data.message != '') {
                    document.getElementById('ebay-category').innerHTML = data.message;
                }
                if (data.success == true) {
                    createNewCategoryLevel(current_level, data.message);
                    if (document.getElementById("ebay_category_" + current_level) &&
                        document.getElementById("ebay_category_" + current_level).value
                        && (data.success == true)) {
                        getEbayCategory(document.getElementById("ebay_category_" + current_level).value, current_level + 1, 0);
                    }
                }
                if (data.success == false && data.message == '') {
                    getCategoryAttributes(value);
                    getCategorySpecifics(value);
                }
                /*  } catch (e) {
                      console.log(e.message);
                      if (document.getElementById('ebay-category')) {
                          document.getElementById('ebay-category').innerHTML = e.message  ;
                      }
                  }*/

            }
        });
    }

    function createNewCategoryLevel(level, array) {
        var container_div;
        if (document.getElementById("select-container-level-" + level)) {
            container_div = document.getElementById("select-container-level-" + level);
        } else {
            container_div = document.createElement("div");
            container_div.id = "select-container-level-" + level;
        }
        container_div.style.padding = '5px';
        if (array != '<h1>Unable to fetch category</h1>') {
            var fieldset_wrapper = document.getElementById("ebay-category");
            if (fieldset_wrapper) {
                fieldset_wrapper.appendChild(container_div);
                var selectList = document.createElement("select");
                selectList.id = "ebay_category_" + level;
                selectList.name = "profile_ebay_categories[level_" + level + "]";
                selectList.className = 'required-entry input-text required-entry form-control';
                container_div.innerHTML = "";
                container_div.appendChild(selectList);
                selectList.innerHTML = array;
                $("#ebay_category_" + level).on('change', function () {
                    var isLeaf = $(this).find(':selected').attr('isLeaf');
                    if (isLeaf != 1) {
                        isLeaf = 0;
                    }
                    getEbayCategory(this.value, level + 1, isLeaf);
                });
            }
        }
        var children = document.getElementById('ebay-category');
        if (children) {
            var elementlengh = children.childElementCount;
            for (var i = level + 1; i <= elementlengh; i++) {
                document.getElementById("select-container-level-" + i).remove();
            }
        }

    }

    function getCategoryAttributes(catId) {
        var url = 'index.php?route=cedebay/profile/getCategoryAttributes&token=<?php echo $token; ?>';
        if (catId != '') {
            var query = $.ajax({
                type: 'POST',
                url: url,
                data: {
                    ebay_profile_id: $("input[name=id]").val(),
                    category_id: catId,
                },
                success: function (res) {
                    try {
                        var data = JSON.parse(res);
                        if (data.success == true) {
                            var attr = data.message;
                            document.getElementById('profile_attribute_mapping_section').innerHTML = attr;
                        }
                    } catch (e) {
                        document.getElementById('profile_attribute_mapping_section').innerHTML = e.message;
                    }
                }
            });
        }
    }

    function getCategorySpecifics(catId) {
        var url = 'index.php?route=cedebay/profile/getCategorySpecifics&token=<?php echo $token; ?>';
        if (catId != '') {
            var query = $.ajax({
                type: 'POST',
                url: url,
                data: {
                    ebay_profile_id: $("input[name=id]").val(),
                    category_id: catId,
                },
                success: function (res) {
                    try {
                        var data = JSON.parse(res);
                        if (data.success == true) {
                            var attr = data.message;
                            document.getElementById('profileEbaySettings').innerHTML = attr;
                            changePriceVariation();
                        }
                    } catch (e) {
                        document.getElementById('profileEbaySettings').innerHTML = e.message;
                    }
                }
            });
        }
    }

    function changeMapping(e) {
        var code = e.getAttribute('data-id');
    }

    var profile_id = '<?php echo $profile_id; ?>';

    // Manufacturer
    $('input[name=\'manufacturer\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                    json.unshift({
                        manufacturer_id: 0,
                        name: '--None--'
                    });
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['manufacturer_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name=\'product_manufacturer\']').val('');

            $('#product_manufacturer' + item['value']).remove();

            $('#product_manufacturer').append('<div id="product_manufacturer' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_manufacturer[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#product_manufacturer').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });
    // Store Category
    $('input[name=\'category\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['category_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name=\'category\']').val('');

            $('#product-category' + item['value']).remove();

            $('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#product-category').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });
    function enableDomesticShipping() {
        var attr = $('#enable_domestic_shipping').val();
        if (attr == '1') {
            $('#domestic_shipping_container').show();
        } else {
            $('#domestic_shipping_container').hide();
        }
    }
    function enableIntShipping() {
        var attr = $('#enable_international_shipping').val();
        if (attr == '1') {
            $('#international_shipping_container').show();
        } else {
            $('#international_shipping_container').hide();
        }
    }
    function changePriceVariation() {
        let v = $("#price_variation_type").val();
        console.log(v != 'default');
        if(v != 'default') {
            $("#price_variation_value_section").show();
        } else {
            $("#price_variation_value_section").hide();
        }
    }
    function addDomShippingRule() {
        var next_row = $("#domestic_shipping tbody").find("tr").length + 1;
        if (next_row < 5) {
            var new_row = "";
            new_row += "<tr id='" + next_row + "'>";
            new_row += "<td><select class='form-control' name='profile_specifics[domestic_shipping][" + next_row + "][shipping_service]'>";
        <?php foreach ($domestic_shipping_services as $domService){ ?>
                new_row+= "<option value='<?php echo $domService['value'];?>'><?php echo $domService['label'];?></option>";
            <?php }?>
            new_row += "</select></td>";
            new_row += "<td><input class='form-control' onkeypress='numberValidate(event, this)' placeholder='Shipping Charge' class='input-text shipping_charge' id='shipping_cost_" + next_row + "' type='text' style='width:50%;'" +
                "value='' name='profile_specifics[domestic_shipping][" + next_row + "][shipping_cost]'></td>";
            new_row += "<td><input class='form-control' onkeypress='numberValidate(event, this)' placeholder='Additional Charge' class='input-text additional_charge' type='text' id='additional_cost_" + next_row + "' style='width:50%;'" +
                "value='' name='profile_specifics[domestic_shipping][" + next_row + "][additional_cost]'></td>";
            new_row += "<td><button class='btn btn-danger' onclick='deleteRow(this)'>";
            new_row += "<span><i class='fa fa-eraser'></i></span></button></td>";
            new_row += "</tr>";
            $("#domestic_shipping tbody:last-child").append(
                new_row);
        }
    }
    function addIntShippingRule() {
        var next_row = $("#international_shipping tbody").find("tr").length + 1;
        if (next_row < 6) {
            var new_row = "";
            new_row += "<tr id='" + next_row + "'>";
            new_row += "<td><select class='form-control' name='profile_specifics[international_shipping][" + next_row + "][shipping_service]'>";
        <?php foreach ($international_shipping_services as $intService){ ?>
                new_row+= "<option value='<?php echo $intService['value'];?>'><?php echo $intService['label'];?></option>";
            <?php }?>
            new_row += "</select></td>";
            new_row += "<td><input class='form-control' onkeypress='numberValidate(event, this)' placeholder='Shipping Charge' class='input-text shipping_charge' id='shipping_cost_" + next_row + "' type='text' style='width:50%;'" +
                "value='' name='profile_specifics[international_shipping][" + next_row + "][shipping_cost]'></td>";
            new_row += "<td><input class='form-control' onkeypress='numberValidate(event, this)' placeholder='Additional Charge' class='input-text additional_charge' type='text' id='additional_cost_" + next_row + "' style='width:50%;'" +
                "value='' name='profile_specifics[international_shipping][" + next_row + "][additional_cost]'></td>";
            new_row += "<td><button class='btn btn-danger' onclick='deleteRow(this)'>";
            new_row += "<span><i class='fa fa-eraser'></i></span></button></td>";
            new_row += "</tr>";
            $("#international_shipping tbody:last-child").append(
                new_row);
        }
    }
    function deleteRow(obj)
    {
        $(obj).closest("tr").remove();
    }
    function numberValidate(event, el) {
        var attr = el.placeholder;
        const charCode = (event.which) ? event.which : event.keyCode;
        if (charCode !== 46 && charCode!== 8 && charCode!==8 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

</script>

<style>
    .form-control {
        margin-bottom: 15px;
    }
</style>
<style type="text/css">
    #overlay {
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2;
        cursor: pointer;
    }

    #text {
        position: absolute;
        top: 50%;
        left: 50%;
        font-size: 50px;
        color: white;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
    }

    .required {
        /*color: red;*/
        /*font-weight: bold;*/
    }

    .option_input {
        margin: 5px 14px 0 0;
        width: 40%;
        border: 1px solid #cccccc;
        background-color: #ffffff;
        border-radius: 3px;
        color: #555555;
        height: 35px;
        margin-bottom: 15px;
        line-height: 1.42857143;
        padding: 8px 13px;
        font-size: 12px;
    }
</style>
<?php echo $footer; ?>

