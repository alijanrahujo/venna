<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="gallery_detail_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_gallery"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit['title']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" id="gallery_detail_id" name="gallery_detail_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $('#gallery_detail_form').submit(function(e) {
        e.preventDefault();

        var update_gallery_detail = new FormData(this);
        update_gallery_detail.set('access_token', $("#access_token").val());

        axios.post(address + 'Gallery/update_gallery_detail' , update_gallery_detail, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Gallery/edit/' + response.data.data);
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