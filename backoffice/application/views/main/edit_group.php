<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="group-form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Group</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Group Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" required>
                                                </fieldset>
                                            </div>
                                        </div>  
                                        <table id="group_privileges_list" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                                <tr>  
                                                    <th style="width: 15%;">#</th>
                                                    <th><?php echo $this->lang->line("group_category"); ?></th>
                                                    <th><?php echo $this->lang->line("group_name"); ?></th>
                                                </tr>  
                                            </thead>
                                        </table>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Update</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="group_id" id="group_id" value="<?php echo $this->uri->segment(3); ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var get_group = new FormData();
        var group_id = $("#group_id").val();
        get_group.set('access_token', $("#access_token").val());
        get_group.set('group_id', group_id);
        get_group.set('language', '<?php echo $this->session->userdata("site_lang"); ?>');
        axios.post(address + 'Admin/get_group_info' , get_group, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $('#name').val(response.data.data.info.name);
                $('#group_privileges_list').dataTable().fnClearTable();
                $('#group_privileges_list').dataTable().fnDestroy();
                $('#group_privileges_list').DataTable({
                    searching: false,
                    lengthChange: false,
                    filter: false,
                    info: false,
                    paginate: false,
                    data: response.data.data.permission,
                });
            }else{
                error_response();
            }
        });
    });

    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Admin";
    });

    $('#group-form').submit(function(e) {
        e.preventDefault();
        
        var update_group = new FormData(this);
        update_group.set('access_token', $("#access_token").val());
        update_group.set('group_id', <?php echo $this->uri->segment(3); ?>);
        update_group.set('language', '<?php echo $this->session->userdata("site_lang"); ?>');

        axios.post(address + 'Admin/update_group' , update_group, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Admin/edit/<?php echo $this->uri->segment(3); ?>');
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