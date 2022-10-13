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
	<link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/vendors/css/select2.min.css">
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
<?php
    $access_token = $this->session->userdata('access_token');
    $verify = $this->Api_Model->get_rows_info(TBL_USER, "*", array('access_token' => $access_token, 'active' => 1));

    if(!isset($verify) || empty($verify) || empty($this->user_profile_info)){
        redirect(base_url() . "Login", "refresh");
    }else{
?>
<body class="vertical-layout vertical-menu 2-columns  navbar-sticky" data-menu="vertical-menu" data-col="2-columns">
    <nav class="navbar navbar-expand-lg navbar-light header-navbar navbar-fixed">
        <div class="container-fluid navbar-wrapper">
            <div class="navbar-header d-flex">
                <div class="navbar-toggle menu-toggle d-xl-none d-block float-left align-items-center justify-content-center" data-toggle="collapse"><i class="ft-menu font-medium-3"></i></div>
            </div>
            <div class="navbar-container">
                <div class="collapse navbar-collapse d-block" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <!-- <li class="i18n-dropdown dropdown nav-item mr-2"><a class="nav-link d-flex align-items-center dropdown-toggle dropdown-language" id="dropdown-flag" href="javascript:;" data-toggle="dropdown"><i class="ft-settings font-medium-3"></i>&nbsp;<span class="selected-language d-md-flex d-none">Language</span></a>
                            <div class="dropdown-menu dropdown-menu-right text-left" aria-labelledby="dropdown-flag">
                                <a class="dropdown-item" href="<?= site_url(); ?>Language/translate_language/english">
                                    <img class="langimg mr-2" src="<?= site_url(); ?>img/flags/us.png" alt="flag"><span class="font-small-3">English</span>
                                </a>
                                <a class="dropdown-item" href="<?= site_url(); ?>Language/translate_language/chinese">
                                    <img class="langimg mr-2" src="<?= site_url(); ?>img/flags/de.png" alt="flag"><span class="font-small-3">Chinese</span>
                                </a>
                            </div>
                        </li> -->
                        <?php
                            if($this->user_profile_info['user_type'] == "ADMIN"){
                                $withdraw_info = $this->Api_Model->get_rows_info(TBL_WITHDRAW, "COUNT(*) as total_withdraw", array('active' => 1, 'status' => "PENDING"));
                                $total_withdraw = isset($withdraw_info['total_withdraw']) ? $withdraw_info['total_withdraw'] : 0;
                        ?>
                        <!-- <li class="dropdown nav-item mr-1"><a class="nav-link d-flex align-items-end" href="<?= site_url() . "Withdraw"; ?>"><i class="fa fa-money" style="font-size: 30px;"></i><div class="number bell-notification"><span><?php echo $total_withdraw; ?></span></div></a></li> -->
                        <?php
                            }
                        ?>
                        <li class="dropdown nav-item mr-1"><a class="nav-link dropdown-toggle user-dropdown d-flex align-items-end" id="dropdownBasic2" href="javascript:;" data-toggle="dropdown">
                                <?php
                                    // $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "name", array('id' => $this->user_profile_info['rank'], 'active' => 1));
                                ?>
                                <div class="user d-md-flex d-none mr-2"><span class="text-right"><?php echo $this->user_profile_info['username']; ?></span><span class="text-right text-muted font-small-3"><?php echo $this->user_profile_info['username']; ?></span><span class="text-right text-muted font-small-3"></span></div><img class="avatar" src="<?= site_url(); ?>library/img/portrait/small/avatar-s-1.png" alt="avatar" height="35" width="35">
                            </a>
                            <div class="dropdown-menu text-left dropdown-menu-right m-0 pb-0" aria-labelledby="dropdownBasic2">
                                <?php
                                    if($this->user_profile_info['user_type'] == "MEMBER"){
                                ?>
                                <a class="dropdown-item" href="<?= site_url() . "Profile"; ?>">
                                    <div class="d-flex align-items-center"><i class="ft-user mr-2"></i><span>Edit Profile</span></div>
                                </a>
                                <?php
                                    }
                                ?>
                                <a class="dropdown-item" href="<?= site_url() . "Profile/password"; ?>">
                                    <div class="d-flex align-items-center"><i class="ft-lock mr-2"></i><span>Change Password</span></div>
                                </a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="<?= site_url() . "Login/logout"; ?>">
                                    <div class="d-flex align-items-center"><i class="ft-power mr-2"></i><span>Logout</span></div>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="wrapper">
        <div class="app-sidebar menu-fixed" data-background-color="man-of-steel" data-image="<?= site_url(); ?>library/img/sidebar-bg/01.jpg" data-scroll-to-active="true">
            <div class="sidebar-header">
                <div class="logo clearfix">
                    <a class="logo-text">
                        <!-- <div class="logo-img"><img src="<?= site_url(); ?>img/logo.png" width="150"></div> -->
                    </a>
                </div>
            </div>
            <div class="sidebar-content main-menu-content">
                <div class="nav-container">
                    <ul class="navigation navigation-main" id="side-menu-nav" data-menu="menu-navigation">
                        
                    </ul>
                </div>
            </div>
            <div class="sidebar-background"></div>
        </div>

        <input type="hidden" id="access_token" value="<?php echo $this->session->userdata("access_token"); ?>">
        <input type="hidden" id="id" value="<?php echo $this->user_profile_info['id']; ?>">
        <input type="hidden" id="insert_by" value="<?php echo $this->user_profile_info['username']; ?>">

<?php
    }
?>

<script>
    var sidebar = new FormData();
    sidebar.set('id', $("#id").val());
    sidebar.set('access_token', $("#access_token").val());
    sidebar.set('language', "<?php echo $this->session->userdata("site_lang"); ?>");

    axios.post(address + 'Common/get_menu_list' , sidebar, apiHeader)
    .then(function (response){
        var html = generate_menu_list(response.data.data.menu_list);
        $('#side-menu-nav').html(html);
    })
    .catch(function (data){
        console.log(data);
        Swal.fire({
            title: '<?php echo $this->lang->line("error"); ?>',
            text: '<?php echo $this->lang->line("something_error"); ?>',
            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
        })
    });                  

    function generate_menu_list(menu_data){
        var html = '';
        $.each(menu_data, function(index, value){
            var sub_count = value.sub_menu.length;
            if(value.ref == 0){
                if(sub_count > 0){
                    html += '<li class="has-sub nav-item"><a href="javascript:;"><i class="ft-user"></i><span class="menu-title">' + value.name + '</span></a>';
                    html += '<ul class="menu-content">';
                    html += generate_menu_list(value.sub_menu);
                    html += '</ul>';
                    html += '</li>';
                }
                else{
                    // dashboard
                    html += '<li><a href="<?= site_url(); ?>' + value.link + '"><i class="' + value.icon + '"></i><span class="menu-item">' + value.name + '</span></a>';
                }
            }
            else{
                if(sub_count > 0){
                    html += '<li class="has-sub nav-item"><a href="javascript:;"><i class="ft-user"></i><span class="menu-title">' + value.name + '</span></a>';
                    html += '<ul class="menu-content">';
                    html += generate_menu_list(value.sub_menu);
                    html += '</ul>';
                    html += '</li>';
                }
                else{
                    html += '<li><a href="<?= site_url(); ?>' + value.link + '"><i class="' + value.icon + '"></i><span class="menu-item">' + value.name + '</span></a>';
                }
            }
        });

        return html;
    }
</script>