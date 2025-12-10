<div id="ageModal" class="modal fade sandeep_model" tabindex="-1" role="dialog" aria-labelledby="ageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header address-header pb-0">
        <h3 id="ageModalLabel">Age Verification</h3>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close" id="ageModalCloseBtn" style="display:none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="verify-section">
          <p> You must be of legal drinking and purchasing age to purchase this product. Please verify date of birth below. We will verify upon delivery.
</p>
          <div class="input-group mb-3">
            <input type="date" id="dobInput" class="form-control" max="{{ date('Y-m-d') }}" autocomplete="off" />
          </div>
          <p id="dobMessage" class="text-danger"></p>
        </div>

        <div class="restriction-section" style="display:none; text-align:center;">
          
          <h5 style="color:#212529;">Not Eligible to Purchase</h5>
          <p>You must be <strong>21 years or older</strong> to purchase alcoholic beverages. This is a legal requirement.</p>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" id="cancelAgeBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger checkAgeBtn">Verify Age</button>
      </div>
    </div>
  </div>
</div>
<style>

    @media only screen and (max-width: 767px) {

#ageModal .modal-content {
        margin-right: 15px !important;
}
    }
    #ageModal .modal-footer span {
    color: #fff !important;    ;
}
</style>