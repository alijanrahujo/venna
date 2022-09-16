<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Control Panel">
    <meta name="keywords" content="Control Panel">
    <title>Admin Panel</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= site_url(); ?>library/img/ico/favicon.ico">
    <link rel="shortcut icon" type="image/png" href="<?= site_url(); ?>library/img/ico/favicon-32.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/custom.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/components.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/themes/layout-dark.css">
    <link rel="stylesheet" href="<?= site_url(); ?>library/css/plugins/switchery.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/pages/dashboard1.css">
    <link rel='stylesheet' href='<?= site_url(); ?>library/textarea/summernote.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/responsive/1.0.4/css/dataTables.responsive.css'>
    <!-- <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css'> -->
<!-- <link rel='stylesheet' href='https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.css'> -->
    <script src="<?= site_url(); ?>library/js/jquery.1.12.4.min.js"></script>
    <script src="<?= site_url(); ?>library/js/ip.js"></script>
    <script src="<?= site_url(); ?>library/js/axios.min.js"></script>
    <style>
        .bell-notification {
            position: relative;
            font-size: 12px;
        }
        .number{
            height: 20px;
            width:  20px;
            background-color: #d63031;
            border-radius: 20px;
            color: white;
            text-align: center;
            position: absolute;
            left: 20px;
            border-style: solid;
            border-width: 1px;
        }

        table.dataTable th,
        table.dataTable td {
            white-space: nowrap;
        }

        table.dataTable.dtr-inline.collapsed tbody td:first-child:before, table.dataTable.dtr-inline.collapsed tbody th:first-child:before {
            top: unset !important;
            border: unset !important;
            box-shadow: unset !important;
            background-color: unset !important;
            color: gray !important;
            margin-top: 3px;
            font-size: 20px;
        }
    </style>
    <script>
        function success_response(data, redirect = false, redirect_page){
            if(redirect){
                Swal.fire({
                    type: "success",
                    text: data,
                    confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                }).then((result) => {
                    window.location.href = "<?= site_url(); ?>" + redirect_page;
                })
            }else{
                Swal.fire({
                    type: "success",
                    text: data,
                    confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                })
            }
        }

        function success_response_with_timer(data, redirect = false, redirect_page){
            if(redirect){
                Swal.fire({
                    type: "success",
                    text: data,
                    showConfirmButton: false,
                    timer: 1000,
                }).then((result) => {
                    window.location.href = "<?= site_url(); ?>" + redirect_page;
                })
            }else{
                Swal.fire({
                    type: "success",
                    text: data,
                    showConfirmButton: false,
                    timer: 1000,
                })
            }
        }

        function warning_response(data){
            Swal.fire({
                type: "warning",
                text: data,
                confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
            })
        }

        function error_response(){
            Swal.fire({
                type: "error",
                text: '<?php echo $this->lang->line("something_error"); ?>',
                confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
            })
        }
    </script>
</head>
<body>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="wrapper">

            <input type="hidden" id="access_token" value="<?php echo $this->session->userdata("access_token"); ?>">
