<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_insert; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="if (confirm('Are you sure ?')) { $('#form').submit();; }"><i class="fa fa-trash-o"></i></button>
        <a onclick="if (confirm('Are you sure ?')) { $(this).attr('href', '<?php echo $clearcat; ?>');}" data-toggle="tooltip" class="btn btn-info">Update Ebay Categories</a>
        <a onclick="if (confirm('Are you sure ?')) { $(this).attr('href', '<?php echo $clearattr; ?>');}" data-toggle="tooltip" class="btn btn-info">Update Ebay Attributes</a>
        <a onclick="if (confirm('Are you sure ?')) { $(this).attr('href', '<?php echo $clearstorecat; ?>');}" data-toggle="tooltip" class="btn btn-info">Update Ebay Store Categories</a>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                <td class="text-center"><?php if ($sort == 'id') { ?>
                  <a href="<?php echo $sort_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_id; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_id; ?>"><?php echo $column_id; ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php if ($sort == 'profile_name') { ?>
                  <a href="<?php echo $sort_profile_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_profile_name; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_profile_name; ?>"><?php echo $column_profile_name; ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php if ($sort == 'status') { ?>
                  <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php echo $column_action; ?></td>
              </tr>
              </thead>
              <tbody>
              <?php if ($profiles) { ?>
              <?php foreach ($profiles as $profile) { ?>
              <tr>
                <td style="text-align: center;"><?php if ($profile['selected']) { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $profile['id']; ?>" checked="checked" />
                  <?php } else { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $profile['id']; ?>" />
                  <?php } ?></td>
                <td class="text-center"><?php echo $profile['id']; ?></td>
                <td class="text-center"><?php echo $profile['profile_name']; ?></td>
                <td class="text-center"><?php echo $profile['status']; ?></td>
                <td class="text-center">
                  <a href="<?php echo $profile['edit']; ?>" data-toggle="tooltip" title="<?php echo 'Edit' ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                  <a onclick="confirmAction('<?php echo $profile['deletebyid'] ?>')" data-toggle="tooltip" title="<?php echo 'Delete'; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="center" colspan="5"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<script>
    function confirmAction(href) {
        var res = confirm("Are you sure?");
        if (res == true) {
            location = href;
        }
    }
</script>