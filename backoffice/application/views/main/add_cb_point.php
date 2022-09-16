<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="cb_point_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add CB Point</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount" required>
                                                </fieldset>
                                            </div>
                                        </div>
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
    $('#cb_point_form').submit(function(e) {
        e.preventDefault();

        var insert_cb_point = new FormData(this);
        insert_cb_point.set('access_token', $("#access_token").val());
        insert_cb_point.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Member/insert_cb_point' , insert_cb_point, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Member/cb');
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