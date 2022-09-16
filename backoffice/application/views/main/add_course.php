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
                        <form method="POST" id="course_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_course"); ?></h4>
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
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Publisher</label>
                                                    <input type="text" class="form-control" id="publisher" name="publisher" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("description"); ?></label>
                                                    <textarea class="form-control" id="content" name="content"></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("image"); ?></label>
                                                    <input type="file" class="custom-file-input" id="image_input" name="Image" onchange="read_image(this);">
                                                    <img id="preview_image" style="width: 100%; margin-top: 5px;"/>
                                                    <label class="custom-file-label choose-file-input-single-position-top" for="image_input"><?php echo $this->lang->line("choose_file"); ?></label>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
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
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Course";
    });

    $(document).ready(function() {
        $('#content').summernote({
            height: 300,
            callbacks: {
                onImageUpload: function(image) {
                    uploadImage(image[0]);
                }
            }
        });
    });

    function uploadImage(image) {
        var optimize_desc = new FormData();
        optimize_desc.set('access_token', $("#access_token").val());
        optimize_desc.set('image', image);

        axios.post(address + 'Course/optimize_desc' , optimize_desc, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                var image = $('<img>').attr('src', response.data.data);
                $('#content').summernote("insertNode", image[0]);
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

    $('#course_form').submit(function(e) {
        e.preventDefault();

        var insert_course = new FormData(this);
        insert_course.set('access_token', $("#access_token").val());
        insert_course.set('insert_by', $("#insert_by").val());
        insert_course.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

        axios.post(address + 'Course/insert_course' , insert_course, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Add Success !', true, 'Course');
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