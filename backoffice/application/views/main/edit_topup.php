<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="topup_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("manage_topup"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Brand</label>
                                                    <select class="form-control" id="company_id" name="company_id" required>
                                                        <option value="0">Select Brand</option>
                                                        <?php
                                                            foreach($company_list as $row_company){
                                                        ?>
                                                        <option value="<?php echo $row_company['id'] ?>"><?php echo $row_company['name'] ?></option>
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
                                                    <label for="basicInput">Stock</label>
                                                    <select class="form-control" id="type" name="type" required>
                                                        <option value="1">Credit</option>
                                                        <option value="0">Debit</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Amount</label>
                                                    <input type="number" class="form-control" id="amount" name="amount" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
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
    $('#topup_form').submit(function(e) {
        e.preventDefault();

        var edit_topup = new FormData(this);
        edit_topup.set('access_token', $("#access_token").val());
        edit_topup.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Company/edit_topup' , edit_topup, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Success !', true, 'Company/topup');
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