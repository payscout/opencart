<form id="payment" class="form-horizontal">
  <fieldset>
    <legend><?php echo $text_credit_card; ?></legend>
    
    <div id="input-billing_ssn" class="form-group">
      <label class="col-sm-2 control-label" for="input-billing_ssn"><?php echo $entry_ssn; ?><br/><small> for US Customer</small></label>
      <div class="col-sm-10">
        <input type="text" name="cc_billing_ssn" maxlength="4" value="" placeholder="<?php echo $entry_ssn; ?>" class="form-control" />
      </div>
    </div>
    
    <div id="input-billing_dob" class="form-group required">
      <label class="col-sm-2 control-label" for="input-billing_dob"><?php echo $entry_billing_dob; ?></label>
      <div class="col-sm-10">
        <input type="text" name="cc_billing_dob" readonly="readonly" value="" placeholder="<?php echo $entry_billing_dob; ?>" class="form-control date" />
      </div>
    </div>
    <div id="input-cc-number" class="form-group required">
      <label class="col-sm-2 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
      <div class="col-sm-10">
        <input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" class="form-control" />
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
      <div class="col-sm-3">
        <select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
       </div>
       <div class="col-sm-3">
        <select name="cc_expire_date_year" class="form-control">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div id="input-cc-cvv2" class="form-group required">
      <label class="col-sm-2 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
      <div class="col-sm-10">
        <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>"  class="form-control" maxlength="4" />
      </div>
    </div>
  </fieldset>
</form>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('#button-confirm').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/payment/payscout/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',
		cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(json) {
			$("#payment").removeClass("has-error");
			
			if (json['error']) {
				
				if (json['error']['ssn']) {
					$("#input-billing_ssn").addClass('has-error');					
				}
				
				if (json['error']['dob']) {
					$("#input-billing_dob").addClass('has-error');					
				}
				
				if (json['error']['dob']) {
					$("#input-billing_dob").addClass('has-error');					
				}
				
				if (json['error']['cc_number']) {
					$("#input-cc-number").addClass('has-error');					
				}
				
				if (json['error']['cc_cvv2']) {
					$("#input-cc-cvv2").addClass('has-error');					
				}
				
				if (json['error']['message']) {
				alert(json['error']['message']);
				}
			}

			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
//--></script>
