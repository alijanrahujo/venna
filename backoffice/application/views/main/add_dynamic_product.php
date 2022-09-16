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
                        <form method="POST" id="product_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_product"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Product Display ?</label>
                                                    <select class="form-control" name="is_normal">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Promotion Product ?</label>
                                                    <select class="form-control" name="is_promotion" onchange="display_price_box(this)">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
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
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <select class="form-control" id="unit" name="unit" required>
                                                        <option value="1">KG</option>
                                                        <option value="2">BOX</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("content"); ?></label>
                                                    <input type="text" class="form-control" id="content" name="content" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">PV Price</label>
                                                    <input type="text" class="form-control" id="pv_price" name="pv_price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Global <?php echo $this->lang->line("price"); ?> (**All Put Same Price, System Auto Convert**)</label>
                                                    <div class="row">
                                                        <?php
                                                            foreach($currency as $row_currency){
                                                        ?>
                                                        <div class="col-2">
                                                            <span><?php echo $row_currency['name']; ?> <?php echo $this->lang->line("price"); ?></span>
                                                            <input type="hidden" name="currency_id[]" value="<?php echo $row_currency['id']; ?>">
                                                        </div>
                                                        <div class="col-1"></div>
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="attr[<?php echo $row_currency['id']; ?>][global_price]" required>
                                                        </div>
                                                        <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Agent <?php echo $this->lang->line("price"); ?></label>
                                                    <div class="row">
                                                        <?php
                                                            foreach($package as $row_package){
                                                        ?>
                                                        <div class="col-2">
                                                            <span><?php echo $row_package['name']; ?></span>
                                                            <input type="hidden" name="package_id[]" value="<?php echo $row_package['id']; ?>">
                                                        </div>
                                                        <div class="col-1"></div>
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="attr[<?php echo $row_package['id']; ?>][price]" required>
                                                        </div>
                                                        <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("description"); ?></label>
                                                    <textarea class="form-control" id="description" name="description"></textarea>
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

    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Product";
    });

    function uploadImage(image) {
        var optimize_desc_image = new FormData();
        optimize_desc_image.set('access_token', $("#access_token").val());
        optimize_desc_image.set('image', image);

        axios.post(address + 'Product/optimize_desc_image' , optimize_desc_image, apiHeader)
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

    $('#product_form').submit(function(e) {
        e.preventDefault();

        var insert_product = new FormData(this);
        insert_product.set('access_token', $("#access_token").val());
        insert_product.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Product/insert_dynamic_product' , insert_product, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?> !', true, 'Product');
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