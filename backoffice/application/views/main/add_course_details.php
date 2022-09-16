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
                        <form method="POST" id="course_details_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add Course Details</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Embed Url</label>
                                                    <input type="text" class="form-control" id="embed_url" name="embed_url">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" id="course_id" name="course_id" value="<?php echo $id; ?>">
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

    $('#course_details_form').submit(function(e) {
        e.preventDefault();

        var insert_course_details = new FormData(this);
        insert_course_details.set('access_token', $("#access_token").val());
        insert_course_details.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Course/insert_course_details' , insert_course_details, apiHeader)
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