<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="shipment_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Shipment Order</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Name</label>
                                                    <input type="text" class="form-control" id="s_name" name="s_name" value="<?php echo $edit['s_name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Email</label>
                                                    <input type="text" class="form-control" id="s_email" name="s_email" value="<?php echo $edit['s_email']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Contact</label>
                                                    <input type="text" class="form-control" id="s_contact" name="s_contact" value="<?php echo $edit['s_contact']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Address</label>
                                                    <input type="text" class="form-control" id="s_address" name="s_address" value="<?php echo $edit['s_address']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping City</label>
                                                    <input type="text" class="form-control" id="s_city" name="s_city" value="<?php echo $edit['s_city']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Postcode</label>
                                                    <input type="text" class="form-control" id="s_postcode" name="s_postcode" value="<?php echo $edit['s_postcode']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping State</label>
                                                    <input type="text" class="form-control" id="s_state" name="s_state" value="<?php echo $edit['s_state']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Country</label>
                                                    <input type="text" class="form-control" id="s_country" name="s_country" value="<?php echo $edit['s_country']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Shipping Remark</label>
                                                    <input type="text" class="form-control" id="s_remark" name="s_remark" value="<?php echo $edit['s_remark']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" id="order_id" name="order_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Order/shipment";
    });

    $('#shipment_form').submit(function(e) {
        e.preventDefault();

        var update_shipment_order = new FormData(this);
        update_shipment_order.set('access_token', $("#access_token").val());
        update_shipment_order.set('update_by', $("#insert_by").val());

        axios.post(address + 'Order/update_shipment_order' , update_shipment_order, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Order/shipment');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });
</script>