<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <?php
                            if($company['mms_level'] != 0){
                        ?>
                        <form method="POST" id="mms_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">MMS</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <?php
                                            foreach($mms as $row_mms){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Level</label>
                                                    <input type="text" class="form-control" readonly value="<?php echo $row_mms['level']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Bonus</label>
                                                    <input type="text" class="form-control" name="attr[<?php echo $row_mms['id'] ?>][bonus]" value="<?php echo $row_mms['bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <input type="hidden" name="mms_id[]" value="<?php echo $row_mms['id'] ?>">
                                        <?php
                                            }
                                        ?>         
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">
                        </form>
                        <?php
                            }
                        ?>
                        <form method="POST" id="bonus_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Bonus</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Bonus Month</label>
                                                    <select class="form-control" name="bonus_month">
                                                        <option value="1" <?php if($company['bonus_month'] == "1"){ echo "selected"; } ?>>Jan</option>
                                                        <option value="2" <?php if($company['bonus_month'] == "2"){ echo "selected"; } ?>>Feb</option>
                                                        <option value="3" <?php if($company['bonus_month'] == "3"){ echo "selected"; } ?>>Mar</option>
                                                        <option value="4" <?php if($company['bonus_month'] == "4"){ echo "selected"; } ?>>Apr</option>
                                                        <option value="5" <?php if($company['bonus_month'] == "5"){ echo "selected"; } ?>>May</option>
                                                        <option value="6" <?php if($company['bonus_month'] == "6"){ echo "selected"; } ?>>Jun</option>
                                                        <option value="7" <?php if($company['bonus_month'] == "7"){ echo "selected"; } ?>>Jul</option>
                                                        <option value="8" <?php if($company['bonus_month'] == "8"){ echo "selected"; } ?>>Aug</option>
                                                        <option value="9" <?php if($company['bonus_month'] == "9"){ echo "selected"; } ?>>Sep</option>
                                                        <option value="10" <?php if($company['bonus_month'] == "10"){ echo "selected"; } ?>>Oct</option>
                                                        <option value="11" <?php if($company['bonus_month'] == "11"){ echo "selected"; } ?>>Nov</option>
                                                        <option value="12" <?php if($company['bonus_month'] == "12"){ echo "selected"; } ?>>Dec</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>       
                                        <br>
                                        <?php
                                            if($company['is_released'] == 0){
                                        ?>
                                        <input type="hidden" name="is_active" value="1">
                                        <button class="btn btn-success" type="submit">Active</button>
                                        <?php
                                            }else{
                                        ?>
                                        <input type="hidden" name="is_active" value="0">
                                        <button class="btn btn-danger" type="submit">Deactive</button>
                                        <?php
                                            }
                                        ?>
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
    $('#mms_form').submit(function(e) {
        e.preventDefault();

        var update_mms = new FormData(this);
        update_mms.set('access_token', $("#access_token").val());

        axios.post(address + 'Company/update_mms' , update_mms, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Company/bonus');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    $('#bonus_form').submit(function(e) {
        e.preventDefault();

        var update_bonus_month = new FormData(this);
        update_bonus_month.set('access_token', $("#access_token").val());

        axios.post(address + 'Company/update_bonus_month' , update_bonus_month, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Company/bonus');
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