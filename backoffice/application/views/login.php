<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Control Panel">
    <meta name="keywords" content="Control Panel">
    <title>Login Page</title>
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
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/components.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/themes/layout-dark.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/plugins/switchery.css">
    <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>library/css/pages/authentication.css">
    <style>
        .auth-page:not(.layout-dark) {
            background: linear-gradient(to left, #595353de, #2d2a27c7), url(<?= site_url(); ?>img/bg-new.jpeg) !important;
            background-size: cover !important;
        }
    </style>
</head>
<body class="vertical-layout vertical-menu 1-column auth-page navbar-sticky blank-page" data-menu="vertical-menu" data-col="1-column">
    <div class="wrapper">
        <div class="main-panel">
            <div class="main-content">
                <div class="content-overlay"></div>
                <div class="content-wrapper">
                    <section id="login" class="auth-height">
                        <div class="row full-height-vh m-0">
                            <div class="col-12 d-flex align-items-center justify-content-center">
                                <div class="card overflow-hidden">
                                    <div class="card-content">
                                        <div class="card-body auth-img">
                                            <div class="row m-0">
                                                <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center auth-img-bg p-3">
                                                    <img src="<?= site_url(); ?>library/img/gallery/login.png" alt="" class="img-fluid" width="300" height="230">
                                                </div>
                                                <div class="col-lg-6 col-12 px-4 py-3">
                                                    <h4 class="mb-2 card-title">Login</h4>
                                                    <p>Welcome back, please login to your account.</p>
                                                    <?php
                                                        if($this->session->flashdata("error") != NULL){
                                                    ?>
                                                    <div class="example-alert">
                                                        <div class="alert alert-danger alert-icon">
                                                            <em class="icon ni ni-cross-circle"></em> <?php echo $this->session->flashdata("error"); ?> </div>
                                                    </div>
                                                    <br>
                                                    <?php
                                                        }
                                                    ?>
                                                    <form method="POST" action="<?= site_url() . "Login/login_post"; ?>">
                                                        <input type="text" name="username" class="form-control mb-3" placeholder="Username">
                                                        <input type="password" name="password" class="form-control mb-2" placeholder="Password">
                                                        <div class="d-sm-flex justify-content-between mb-3 font-small-2">
                                                            <div class="remember-me mb-2 mb-sm-0">
                                                            </div>
                                                            <a>Forgot Password?</a>
                                                        </div>
                                                        <div class="d-flex justify-content-between flex-sm-row flex-column">
                                                            <button type="submit" class="btn btn-primary">Login</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= site_url(); ?>library/vendors/js/vendors.min.js"></script>
    <script src="<?= site_url(); ?>library/vendors/js/switchery.min.js"></script>
    <script src="<?= site_url(); ?>library/js/core/app-menu.js"></script>
    <script src="<?= site_url(); ?>library/js/core/app.js"></script>
    <script src="<?= site_url(); ?>library/js/notification-sidebar.js"></script>
    <script src="<?= site_url(); ?>library/js/customizer.js"></script>
    <script src="<?= site_url(); ?>library/js/scroll-top.js"></script>
</body>
</html>