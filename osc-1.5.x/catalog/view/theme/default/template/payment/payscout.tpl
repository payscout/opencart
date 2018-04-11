<h2><?php echo $text_credit_card; ?></h2>
<style>
#payment input[type="text"]{ width:150px; padding:5px;}#payment select{ width:76px; padding:5px;}
</style>
<div class="content" id="payment">
  <table class="form">
  <tr>
      <td><?php echo $entry_ssn; ?><br/><small> for US Customer</small></td>
      <td>
        <input type="text" name="cc_billing_ssn" maxlength="4" value="" placeholder="<?php echo $entry_ssn; ?>" />
      </td>
    </tr>
  <tr>
      <td><?php echo $entry_dob; ?></td>
      <td><input type="text" name="cc_dob" class="date" value="" readonly="readonly" placeholder="<?php echo $entry_dob; ?>"/></td>
    </tr>   
    <tr>
      <td><?php echo $entry_cc_number; ?></td>
      <td><input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_expire_date; ?></td>
      <td><select name="cc_expire_date_month">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="cc_expire_date_year">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td><?php echo $entry_cc_cvv2; ?></td>
      <td><input type="text" name="cc_cvv2" value="" size="3" /></td>
    </tr>
    
  </table>
</div>
<div class="buttons">
  <div class="right"><input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" /></div>
</div>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	if ($.browser.msie && $.browser.version == 6) {
		$('.date').bgIframe();
	}

	$('.date').datepicker({dateFormat: 'mm/dd/yy', changeMonth: true,
            changeYear: true, yearRange: "-60:-10"});	
});
//--></script> 
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/payscout/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			
			var error_message = '';
			
			if (json['error']) {
				
					if(json['error']['cc_billing_ssn'])
					{
						error_message += json['error']['cc_billing_ssn'] + "\n";
					}
					
					if(json['error']['dob'])
					{
						error_message += json['error']['dob'] + "\n";
					}
					
					if(json['error']['dob'])
					{
						error_message += json['error']['dob'] + "\n";
					}					
										
					if(json['error']['cc_number'])
					{
						error_message += json['error']['cc_number'] + "\n";
					}
					
					if(json['error']['cc_cvv2'])
					{
						error_message += json['error']['cc_cvv2'] + "\n";
					}
					
					if(json['error']['message'])
					{
						error_message += json['error']['message'] + "\n";
					}
					
					alert(error_message);
			}
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
//--></script>