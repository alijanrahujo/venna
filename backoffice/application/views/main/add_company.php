<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="company_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_company"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <select class="form-control" id="type" name="type" required onchange="select_type(this)">
                                                        <option value="0"><?php echo $this->lang->line("select_type"); ?></option>
                                                        <option value="1"><?php echo $this->lang->line("fixed"); ?></option>
                                                        <option value="2"><?php echo $this->lang->line("dynamic"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="name" name="name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row" id="global_price_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("global"); ?> <?php echo $this->lang->line("price"); ?></label>
                                                    <input type="text" class="form-control" id="price" name="price">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("username"); ?></label>
                                                    <input type="text" class="form-control" id="username" name="username" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("password"); ?></label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("cfm_password"); ?></label>
                                                    <input type="password" class="form-control" id="cfm_password" name="cfm_password" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("bank_name"); ?></label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("account_name"); ?></label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("account_no"); ?></label>
                                                    <input type="number" class="form-control" id="account_no" name="account_no" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
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
    function select_type(data){
        if(data.value == 1){
            $("#global_price_box").css("display", "flex");
        }else{
            $("#global_price_box").css("display", "none");
        }
    }

    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Company";
    });

    $('#company_form').submit(function(e) {
        e.preventDefault();

        var insert_company = new FormData(this);
        insert_company.set('access_token', $("#access_token").val());
        insert_company.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Company/insert_company' , insert_company, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Company');
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