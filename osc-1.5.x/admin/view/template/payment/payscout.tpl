<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          
          <tr>
            <td><span class="required">*</span> <?php echo $entry_username; ?></td>
            <td><input type="text" name="payscout_username" value="<?php echo $payscout_username; ?>" />
              <?php if ($error_username) { ?>
              <span class="error"><?php echo $error_username; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_password; ?></td>
            <td><input type="text" name="payscout_password" value="<?php echo $payscout_password; ?>" />
              <?php if ($error_password) { ?>
              <span class="error"><?php echo $error_password; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><span class="required">*</span> <?php echo $entry_token; ?></td>
            <td><input type="text" name="payscout_token" value="<?php echo $payscout_token; ?>" />
              <?php if ($error_token) { ?>
              <span class="error"><?php echo $error_token; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td>&nbsp;<?php echo $entry_total; ?></td>
            <td><input type="text" name="payscout_total" value="<?php echo $payscout_total; ?>" /></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_transaction; ?></td>
            <td><select name="payscout_server" id="input-server">
                <?php if ($payscout_server) { ?>
                <option value="1" selected="selected"><?php echo $text_live; ?></option>
                <option value="0"><?php echo $text_test; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_live; ?></option>
                <option value="0" selected="selected"><?php echo $text_test; ?></option>
                <?php } ?>
              </select></td>
          </tr>
         
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="payscout_order_status_id" id="input-order-status">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $payscout_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>        
         
            <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="payscout_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $payscout_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="payscout_status">
                <?php if ($payscout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="payscout_sort_order" value="<?php echo $payscout_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 