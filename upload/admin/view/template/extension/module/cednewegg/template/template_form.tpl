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
                    <input type="hidden" name="id" id="input-id" value="<?php echo $id;?>"/>
                    <div class="">

                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-title"><?php echo $entry_template_name; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="template_name" id="input-title" value="<?php echo $template_name;?>"
                                           class="form-control"/>
                                </div>
                            </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $entry_template_data; ?></label>
                            <div class="col-sm-10">
                                <textarea name="template_data" rows="5" id="template_data" placeholder="<?php echo $entry_template_data; ?>"  class="form-control summernote"><?php echo isset($template_data) ? $template_data : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-lg-3 required">
                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="Attribute" data-original-title="">
                    Attribute
                </span>
                            </label>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" id="template_variable_selector">
                                            <option></option>
                                            <optgroup label="Product Fields"></optgroup>
                                            <?php if (isset($template_product_variables) && !empty($template_product_variables)){ ?>
                                                <?php foreach ($template_product_variables as $varKey => $templateVar){ ?>
                                                    <option value="<?php echo $varKey ?>"><?php echo $templateVar ?></option>
                                                 <?php } ?>
                                            <?php } ?>
                                            <optgroup label="Product Attributes"></optgroup>

                                            <?php if (isset($template_product_attributes) && !empty($template_product_attributes)){ ?>
                                            <?php foreach ($template_product_attributes as $attr){ ?>
                                            <option value="<?php echo $attr['attribute_id'] ?>"><?php echo $attr['name'] ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" onclick="addTempVariable()" class="btn btn-primary">Add Attribute</button>
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
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script>
    function addTempVariable() {
        var val = $("#template_variable_selector").val();
        if(val && val.trim() != '') {
            $("#template_data").summernote('insertText', val);
        }
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

