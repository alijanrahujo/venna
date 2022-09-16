<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="voucher_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_voucher"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("username"); ?></label>
                                                    <input type="text" class="form-control" id="username" name="username">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row" id="package_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("package"); ?></label>
                                                    <select class="form-control" id="package_id" name="package_id">
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row" id="country_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("country"); ?></label>
                                                    <select class="form-control" id="country_id" name="country_id">
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <div id="submit_box" style="display: none;">
                                            <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                        </div>
                                        <div id="search_box">
                                            <button class="btn btn-primary" type="button" onclick="search_username()"><?php echo $this->lang->line("search"); ?></button>
                                        </div>
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
    var country_list = [];
    var package_list = [];
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Voucher/package";
    });

    function search_username(){
        var username = $("#username").val();

        var search_username = new FormData();
        search_username.set('access_token', $("#access_token").val());
        search_username.set('insert_by', $("#insert_by").val());
        search_username.set('username', username);

        axios.post(address + 'Voucher/search_username' , search_username, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $("#search_box").hide();
                $("#submit_box").show();
                package_list = response.data.data.package;
                if(package_list == ""){
                    $("#package_id").prop("disabled", true);
                    $('#package_id').empty().append($('<option value="0">').text("No Package Found"));
                }else{
                    $("#package_id").prop("disabled", false);
                    $('#package_id').empty().append($('<option value="0">').text("Select Package"));
                    for (var i = 0; i < package_list.length; i++) {
                        $('#package_id').append($('<option value="' + package_list[i].id + '">').text(package_list[i].package_name));
                    }
                }

                country_list = response.data.data.country;
                if(country_list == ""){
                    $("#country_id").prop("disabled", true);
                    $('#country_id').empty().append($('<option value="0">').text("No Country Found"));
                }else{
                    $("#country_id").prop("disabled", false);
                    $('#country_id').empty().append($('<option value="0">').text("Select Country"));
                    for (var i = 0; i < country_list.length; i++) {
                        $('#country_id').append($('<option value="' + country_list[i].id + '">').text(country_list[i].name));
                    }
                }
                $("#package_box").show();
                $("#country_box").show();
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    $('#voucher_form').submit(function(e) {
        e.preventDefault();

        var insert_voucher_package = new FormData(this);
        insert_voucher_package.set('access_token', $("#access_token").val());
        insert_voucher_package.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Voucher/insert_voucher_package' , insert_voucher_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Voucher/package');
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