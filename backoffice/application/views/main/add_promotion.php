<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="promotion_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Promotion</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Unit Price</label>
                                                    <input type="text" class="form-control" id="unit_price" name="unit_price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Promotion Price</label>
                                                    <input type="text" class="form-control" id="promotion_price" name="promotion_price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Purchase Quantity</label>
                                                    <input type="text" class="form-control" id="purchase_quantity" name="purchase_quantity" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Free Quantity</label>
                                                    <input type="text" class="form-control" id="free_quantity" name="free_quantity" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                        <input type="hidden" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Product/promotion";
    });

    $('#promotion_form').submit(function(e) {
        e.preventDefault();

        var insert_promotion = new FormData(this);
        insert_promotion.set('access_token', $("#access_token").val());
        insert_promotion.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Product/insert_promotion' , insert_promotion, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Add Success !', true, 'Product/promotion');
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