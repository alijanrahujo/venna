<style>
    .modal-backdrop {
        z-index: unset !important;
        background-color: unset !important;
    }
</style>

<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="delivery_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_delivery_fee"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("country"); ?></label>
                                                    <select class="form-control" id="country_id" name="country_id" required>
                                                        <?php
                                                            foreach($country as $row_country){
                                                        ?>
                                                        <option value="<?php echo $row_country['id'] ?>"><?php echo $row_country['name'] ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <select class="form-control" id="type" name="type" required>
                                                        <option value="1">KG</option>
                                                        <option value="2">BOX</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("region"); ?></label>
                                                    <select class="form-control" id="region" name="region" required>
                                                        <option value="1">West Malaysia</option>
                                                        <option value="2">East Malaysia</option>
                                                        <option value="3">Overseas</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("delivery_company"); ?></label>
                                                    <input class="form-control" type="text" id="delivery_company" name="delivery_company" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("start"); ?></label>
                                                    <input class="form-control" type="text" id="start" name="start" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("end"); ?></label>
                                                    <input class="form-control" type="text" id="end" name="end" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("price"); ?></label>
                                                    <input class="form-control" type="text" id="price" name="price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Delivery";
    });

    $('#delivery_form').submit(function(e) {
        e.preventDefault();

        var insert_delivery = new FormData(this);
        insert_delivery.set('access_token', $("#access_token").val());
        insert_delivery.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Delivery/insert_delivery' , insert_delivery, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?> !', true, 'Delivery');
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