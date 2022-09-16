<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="stock_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("manage_stock"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
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
                                                    <label for="basicInput">Agent Username</label>
                                                    <input type="text" class="form-control" id="agent_username" name="agent_username" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Stock</label>
                                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
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
                            <?php
                                $company_id = $this->user_profile_info['company_id'];
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
    $('#stock_form').submit(function(e) {
        e.preventDefault();

        var edit_stock = new FormData(this);
        edit_stock.set('access_token', $("#access_token").val());
        edit_stock.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Company/edit_stock' , edit_stock, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Success !', true, 'Company/stock');
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