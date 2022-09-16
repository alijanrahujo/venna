<style>
    .modal-backdrop {
        z-index: unset !important;
        background-color: unset !important;
    }
</style>

<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="slider_form" enctype="multipart/form-data">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_slider"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("image"); ?></label>
                                                    <input type="file" class="custom-file-input" id="image_input" name="file" onchange="read_image(this);">
                                                    <img id="preview_image" style="width: 100%; margin-top: 5px;" src="<?php echo DISPLAY_PATH . "img/slider/" . $edit['image']; ?>"/>
                                                    <label class="custom-file-label choose-file-input-single-position-top" for="image_input"><?php echo $this->lang->line("choose_file"); ?></label>
                                                </fieldset>
                                            </div>
                                        </div>                 
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" name="slider_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Slider";
    });

    function read_image(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $(input).next('#preview_image')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
        $("#image_input").prop( "disabled", false);
    }

    $('#slider_form').submit(function(e) {
        e.preventDefault();

        var update_slider = new FormData(this);
        update_slider.set('access_token', $("#access_token").val());
        update_slider.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Slider/update_slider' , update_slider, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?> !', true, 'Slider/edit/' + response.data.data);
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