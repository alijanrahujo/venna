<link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/dropzone.min.css">
<link rel="stylesheet" href="<?= site_url(); ?>library/css/plugins/ex-component-upload.css">
<style>
    .dz-button {
        border: none !important;
        color: gray;
        background: none !important;
    }
</style>
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
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_gallery"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <input type="text" class="form-control" readonly value="<?php echo $edit['type']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit['name']; ?>">
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
                            <input type="hidden" id="gallery_id" name="gallery_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" id="upload_form" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Title</label>
                                                    <input type="text" class="form-control" name="title">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">File</label>
                                                    <input type="file" class="form-control" name="file">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                        <input type="hidden" name="gallery_id" value="<?php echo $id; ?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        if(!empty($attachment)){
                            
                    ?>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <?php
                                            $count = 0;
                                            foreach($attachment as $row_attachment){
                                                $count++;
                                                $sequence_id = $row_attachment['sequence'];
                                                $gallery_detail_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row_attachment['id']));
                                        ?>
                                        <div class="col-md-4" id="div<?php echo $sequence_id; ?>" ondrop="drop(event, this)" ondragover="allowDrop(event)" rel="<?php echo $sequence_id; ?>">
                                            <?php
                                                if($row_attachment['attachment_type'] == "pdf"){
                                            ?>
                                            <object data="<?php echo DISPLAY_PATH . "img/gallery/" . $row_attachment['gallery_id'] . "/" . $row_attachment['name']; ?>" width="100%" type="application/pdf"> <a href="<?php echo DISPLAY_PATH . "img/gallery/" . $row_attachment['gallery_id'] . "/" . $row_attachment['name']; ?>" target="_blank"></a> </object>
                                            <?php
                                                }else{
                                            ?>
                                            <img src="<?php echo DISPLAY_PATH . "img/gallery/" . $row_attachment['gallery_id'] . "/" . $row_attachment['name']; ?>" style="width: 100%;" draggable="true" ondragstart="drag(event)" id="<?php echo $sequence_id; ?>"><br><br>
                                            <?php
                                                }
                                            ?>
                                            <div style="text-align: center;"><span><b><?php echo $count; ?> -</b></span>&nbsp;&nbsp;&nbsp;<a class="btn btn-info" href="<?php echo site_url() . "Gallery/attachment/" . $gallery_detail_id; ?>">Edit</a> <button type="button" class="btn btn-danger" onclick="delete_attachment(<?php echo $row_attachment['id']; ?>)">Delete</button></div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= site_url(); ?>library/dropzone/dist/dropzone.js"></script>
<script>
    // $(document).ready(function () {
    //     Dropzone.autoDiscover = false;
    //     var myDropzone = new Dropzone("#myDropzone", {
    //         headers: {
    //             'Cache-Control': null,
    //             'X-Requested-With': null,
    //         },
    //         url: address + 'Gallery/upload',
    //         params: {
    //             'access_token':$("#access_token").val()
    //         },
    //         success: function (file, response) {
    //             var json_response = JSON.parse(response);
    //             if(json_response.status == "Success"){
    //                 window.location.href = "<?php echo site_url() . "Gallery/edit/"; ?>" + json_response.data.encrypt_id;
    //             }else{
    //                 warning_response(json_response.message);
    //             }
    //         },
    //         error: function (file, response) {
    //             console.log(data);
    //             error_response();
    //         }
    //     });
    // })

    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Gallery";
    });

    $('#upload_form').submit(function(e) {
        e.preventDefault();

        var upload_gallery = new FormData(this);
        upload_gallery.set('access_token', $("#access_token").val());

        axios.post(address + 'Gallery/upload' , upload_gallery, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                window.location.href = "<?php echo site_url() . "Gallery/edit/"; ?>" + response.data.data.encrypt_id;
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    $('#gallery_form').submit(function(e) {
        e.preventDefault();

        var update_gallery = new FormData(this);
        update_gallery.set('access_token', $("#access_token").val());

        axios.post(address + 'Gallery/update_gallery' , update_gallery, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Gallery/edit/' + response.data.data.encrypt_id);
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    function delete_attachment(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_attachment = new FormData();
            delete_attachment.set('access_token', $("#access_token").val());
            delete_attachment.set('id', id);

            axios.post(address + 'Gallery/delete_attachment' , delete_attachment, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Gallery/edit/' + response.data.data.encrypt_id);
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });
        }
    }

    function allowDrop(ev) {
        ev.preventDefault();
    }
    
    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }
    
    function drop(ev, i) {
        ev.preventDefault();
        var from_sequence = ev.dataTransfer.getData("text");
        var to_sequence = $(i).attr("rel");

        var exchange_sequence = new FormData();
        exchange_sequence.set('access_token', $("#access_token").val());
        exchange_sequence.set('gallery_id', $("#gallery_id").val());
        exchange_sequence.set('from_sequence', from_sequence);
        exchange_sequence.set('to_sequence', to_sequence);

        axios.post(address + 'Gallery/exchange_sequence' , exchange_sequence, apiHeader)
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
    }
</script>