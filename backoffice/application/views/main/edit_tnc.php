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
                        <form method="POST" id="tnc_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_tnc"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Description</label>
                                                    <textarea class="form-control" id="description" name="terms_and_conditions"><?php echo $edit['terms_and_conditions']; ?></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
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

    $('#tnc_form').submit(function(e) {
        e.preventDefault();

        var tnc = new FormData(this);
        tnc.set('access_token', $("#access_token").val());
        tnc.set('update_by', $("#insert_by").val());

        axios.post(address + 'Company/update_tnc' , tnc, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Company/tnce');
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