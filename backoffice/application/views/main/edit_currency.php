<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="currency_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_currency"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("code"); ?></label>
                                                    <input type="text" class="form-control" id="code" name="code" value="<?php echo $edit['code']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("exchange_rate"); ?></label>
                                                    <input type="text" class="form-control" id="exchange_rate" name="exchange_rate" value="<?php echo $edit['exchange_rate']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" name="currency_id" value="<?php echo $id; ?>">
                            <?php
                                $company_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit['company_id']));
                            ?>
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Currency/view/" + $("#company_id").val();
    });

    $('#currency_form').submit(function(e) {
        e.preventDefault();

        var update_currency = new FormData(this);
        update_currency.set('access_token', $("#access_token").val());

        axios.post(address + 'Currency/update_currency' , update_currency, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Currency/view/' + response.data.data.company_id);
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