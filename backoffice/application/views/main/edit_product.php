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
                                    <h4 class="card-title">Edit Product</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Product Display ?</label>
                                                    <select class="form-control" name="is_normal">
                                                        <option value="0" <?php if($edit['is_normal'] == 0){ echo "selected"; } ?>>No</option>
                                                        <option value="1" <?php if($edit['is_normal'] == 1){ echo "selected"; } ?>>Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Promotion Product ?</label>
                                                    <select class="form-control" name="is_promotion">
                                                        <option value="0" <?php if($edit['is_promotion'] == 0){ echo "selected"; } ?>>No</option>
                                                        <option value="1" <?php if($edit['is_promotion'] == 1){ echo "selected"; } ?>>Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Category</label>
                                                    <select class="form-control" name="category_id">
                                                        <option value="0">Please Select Category</option>
                                                        <?php
                                                            foreach($category as $row_category){
                                                        ?>
                                                        <option value="<?php echo $row_category['id']; ?>" <?php if($row_category['id'] == $edit['category_id']){ echo "selected"; } ?>><?php echo $row_category['name']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            if($edit['is_promotion'] == 1){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Price</label>
                                                    <input type="text" class="form-control" id="price" name="price" value="<?php echo $edit['price']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <select class="form-control" id="unit" name="unit" required>
                                                        <option value="1" <?php if("kg" == $edit['unit']){ echo "selected"; } ?>>KG</option>
                                                        <option value="2" <?php if("box" == $edit['unit']){ echo "selected"; } ?>>BOX</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("content"); ?></label>
                                                    <input type="text" class="form-control" id="content" name="content" value="<?php echo $edit['content']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Description</label>
                                                    <textarea class="form-control" id="description" name="description"><?php echo $edit['description']; ?></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Image</label>
                                                    <input type="file" class="custom-file-input" id="image_input" name="Image" onchange="read_image(this);">
                                                    <img id="preview_image" style="width: 100%; margin-top: 5px;"/>
                                                    <label class="custom-file-label choose-file-input-single-position-top" for="image_input">Choose file</label>
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
                            <input type="hidden" id="product_id" name="product_id" value="<?php echo $id; ?>">
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

        var update_product = new FormData(this);
        update_product.set('access_token', $("#access_token").val());
        update_product.set('update_by', $("#insert_by").val());

        axios.post(address + 'Product/update_product' , update_product, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Product');
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