<script id="add-to-basket-form-template" type="text/template"> 
    <div class="modal-content">
        <div class="modal-header">
          <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
          <h4 class="modal-title">Add resource to basket</h4>
        </div>
        <div class="modal-body">
          <div id="add-to-basket-form">
            <div class="crm-section">
              <div class="label">
                <label for="date_from">Date/time from</label>
              </div>
              <div class="content">
                <input type="text" name="date_from" id="date_from" class="dateplugin" autocomplete="off">           
             </div>
              <div class="clear"></div>
            </div>
            <div class="crm-section">
              <div class="label">
                  <label for="date_to">Date/time to</label>
                </div>
                <div class="content">
                  <input name="date_to" type="text" id="date_to" class="dateplugin" class="form-text">
                </div>
                <div class="clear"></div>
              </div>
            </div>
            <div class="crm-section">
                <div class="label">
                  <label for="configuration">Configuration</label>
                </div>
                <div class="content">
                 <select name="configuration" id="configuration" class="form-select">
                    <option value="">- select resource type -</option>
                      <option value="2">Boardroom £300</option>  
                      <option value="1">etc</option>  
                      <option value="3">etc</option>  
                    </select>
                </div>
                 <div class="clear"></div>
              </div>

              <div class="crm-section">
                <div class="label">
                  <label for="quantity">Quantity</label>
                </div>
                <div class="content">
                  <input name="quantity" type="text" id="quantity" class="form-text">
                </div>
                 <div class="clear"></div>
              </div>

               <div class="crm-section">
                <div class="label">
                  <label for="quantity">Note</label>
                </div>
                <div class="content">
                  <textarea> </textarea>
                </div>
                 <div class="clear"></div>
              </div>


              <div class="crm-section">
                 <div class="label">Price estimate</div>
                 <div class="content calulation-panel">

                 </div>
                 <div class="clear"></div>
              </div>

              
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-dismiss="modal">Close</button>
          <button type="button" class="btn">Add to basket</button>
        </div>
      </div><!-- /.modal-content -->

</script>
