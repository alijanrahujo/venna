<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="gallery_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_gallery"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Type</label>
                                                    <select class="form-control" id="type" name="type" required>
                                                        <option value="1">Image</option>
                                                        <option value="2">Video</option>
                                                        <option value="3">Pdf</option>
                                                        <option value="4">Ppt</option>
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
        window.location.href = "<?= site_url(); ?>Gallery";
    });

    $('#gallery_form').submit(function(e) {
        e.preventDefault();

        var insert_gallery = new FormData(this);
        insert_gallery.set('access_token', $("#access_token").val());
        insert_gallery.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Gallery/insert_gallery' , insert_gallery, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Gallery');
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