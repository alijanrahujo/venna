<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="company_section_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_others_section"); ?></h4>
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
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Company/others";
    });

    $('#company_section_form').submit(function(e) {
        e.preventDefault();

        var insert_company_section = new FormData(this);
        insert_company_section.set('access_token', $("#access_token").val());
        insert_company_section.set('insert_by', $("#insert_by").val());
        insert_company_section.set('type', "others");

        axios.post(address + 'Company/insert_company_section' , insert_company_section, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Add Success !', true, 'Company/others');
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