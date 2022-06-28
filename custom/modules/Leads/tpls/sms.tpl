<div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header sms-header">
        <button type="button" class="close cross-icon" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title sms-modal-title" id="smsModalLabel">Send SMS</h4>
      </div>
      <div class="modal-body">
        <form action="index.php?module=Leads&action=sendSMS" method="POST">
            <input type="hidden" name="lead_id" value={$lead_id}>
            <div class="form-group">
                <label for="to_number">Mobile Number:</label>
                <input type="tel" name="to_number" class="form-control" value={$phone_mobile} readonly>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" name="message" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>