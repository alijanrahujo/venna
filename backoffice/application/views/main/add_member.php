<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="agent_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_agent"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("company"); ?></label>
                                                    <select class="form-control" id="company_id" name="company_id" required>
                                                        <option value="0">Select Brand</option>
                                                        <?php
                                                            foreach($company as $row_company){
                                                        ?>
                                                        <option value="<?php echo $row_company['id']; ?>"><?php echo $row_company['name']; ?></option>
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
                                                    <label for="basicInput"><?php echo $this->lang->line("username"); ?></label>
                                                    <input type="text" class="form-control" id="username" name="username" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("email_address"); ?></label>
                                                    <input type="email" class="form-control" id="email" name="email" required>
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
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Member";
    });

    $('#agent_form').submit(function(e) {
        e.preventDefault();

        var insert_member = new FormData(this);
        insert_member.set('access_token', $("#access_token").val());
        insert_member.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Member/insert_member' , insert_member, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Member');
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