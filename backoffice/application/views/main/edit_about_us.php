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
                        <form method="POST" id="about_us_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit About Us</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Title</label>
                                                    <input type="text" class="form-control" name="name" value="<?php echo $section['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Description</label>
                                                    <textarea class="form-control" id="description" name="about_us"><?php echo $content['content']; ?></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" id="section_id" name="section_id" value="<?php echo $id; ?>">
                            <input type="hidden" id="type" value="<?php echo $section['type']; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#description').summernote({
            height: 300,
            callbacks: {
                onImageUpload: function(image) {
                    uploadImage(image[0]);
                }
            }
        });
    });

    function uploadImage(image) {
        var optimize_desc_image = new FormData();
        optimize_desc_image.set('access_token', $("#access_token").val());
        optimize_desc_image.set('image', image);

        axios.post(address + 'Company/optimize_desc_image' , optimize_desc_image, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                var image = $('<img>').attr('src', response.data.data);
                $('#description').summernote("insertNode", image[0]);
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

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

    $('#about_us_form').submit(function(e) {
        e.preventDefault();

        var type = $("#type").val();

        var about_us = new FormData(this);
        about_us.set('access_token', $("#access_token").val());
        about_us.set('update_by', $("#insert_by").val());

        axios.post(address + 'Company/update_about_us' , about_us, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                if(type == "about"){
                    success_response('Update Success !', true, 'Company/about');
                }else{
                    success_response('Update Success !', true, 'Company/others');
                }
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