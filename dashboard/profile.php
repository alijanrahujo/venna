<?php
include("head.php");
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Profile</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Profile</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Type here...">
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            
            
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
           
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
              <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New message</span> from Laur
                        </h6>
                        <p class="text-xs text-secondary mb-0 ">
                          <i class="fa fa-clock me-1"></i>
                          13 minutes ago
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New album</span> by Travis Scott
                        </h6>
                        <p class="text-xs text-secondary mb-0 ">
                          <i class="fa fa-clock me-1"></i>
                          1 day
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(453.000000, 454.000000)">
                                  <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                  <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          Payment successfully completed
                        </h6>
                        <p class="text-xs text-secondary mb-0 ">
                          <i class="fa fa-clock me-1"></i>
                          2 days
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      
      <h3>My Profile</h3>
      <div class="row my-4">
        <div class="col-lg-6 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
            <h5><center>My Information</center></h5>
                
            </div>
            <div class="card-body px-0 pb-2">
            <div class="flex p-2">
            <div class="form-block">
                  <form method="post">
                    <div class="form-group">
                    <label for="password" class="float-left">Email</label>
                    <input type="email" name="email" class="form-control" id="email">
                    </div>
                  <div class="row">
                    <div class="form-group">
                    <label for="password" class="float-left">Username</label>
                    <input type="text" name="password" class="form-control" id="password">
                    </div>
                    <div class="form-group">
                      <label for="password">Full Name</label>
                      <input type="text" name="password" class="form-control " id="password">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email">Phone No:</label>
                      <input type="text" name="email" class="form-control col-md-4" id="email">
                      </div>
                  

                  <input type="submit" value="Submit" class="btn btn-pill text-white btn-block btn-primary">

                 
                </form>
              </div>
</div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-header pb-0">
            <h5><center>Adresss</center></h5>
                
            </div>
            <div class="card-body px-0 pb-2">
            <div class="flex p-2">
            <div class="form-block">
                  <form method="post">
                    <div class="form-group">
                    <label for="text" class="float-left">Name</label>
                    <input type="text" name="name" class="form-control" id="email" placeholder="e.g. home">
                    </div>
                  
                    <div class="form-group">
                    <label for="password" class="float-left">Address</label>
                    <input type="text" name="address" class="form-control" id="password">
                    </div>
                    <div class="row">
                    <div class="form-group col-md-9">
                    <label for="password">City</label>
                    <input type="text" name="password" class="form-control" id="password">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="password">Postal code*</label>
                      <input type="text" name="password" class="form-control " id="password">
                      </div>
                    </div>
                      <div class="form-group">
                      <label for="email">State</label>
                      <input type="text" name="email" class="form-control col-md-4" id="email">
                      </div>
                  

                  <input type="submit" value="Submit" class="btn btn-pill text-white btn-block btn-primary">

                 
                </form>
              </div>
</div>
                </div>
          </div>
        </div>
        <div class="container-fluid py-4">
          <div class="row my-4">
        <div class="col-lg-6 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
            <h5><center>Security</center></h5>
                
            </div>
            <div class="card-body px-0 pb-2">
            <div class="flex p-2">
            <div class="form-block">
                  <form method="post">
                    <div class="form-group">
                    <label for="text" class="float-left">New Security Code</label>
                    <input type="text" name="scode" class="form-control">
                    </div>
                  
                    <div class="form-group">
                    <label for="password" class="float-left">Confirm Security code</label>
                    <input type="text" name="cscode" class="form-control">
                    </div>
                    <div class="form-group">
                    <label for="password">Old Password</label>
                    <input type="password" name="password" class="form-control" id="password">
                    </div>
                    <div class="form-group">
                      <label for="password">New Password</label>
                      <input type="password" name="password" class="form-control " id="password">
                      </div>
                      <div class="form-group">
                      <label for="email">Confirm Password</label>
                      <input type="password" name="email" class="form-control col-md-4" id="email">
                      </div>
                  

                  <input type="submit" value="Submit" class="btn btn-pill text-white btn-block btn-primary">

                 
                </form>
              </div>
</div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="card">
            <div class="card-header pb-0">
            <h5><center>Bank Details</center></h5>
                
                </div>
                <div class="card-body px-0 pb-2">
                <div class="flex p-2">
                <div class="form-block">
                <form id="login_form" method="post">
                    <div class="form-group first">
                    <label for="password">Bank Name</label>
                      <select class="form-control" id="select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                      </select>
                    </div>
                    <div class="form-group last mb-4">
                    <label for="password">Bank Account Holder</label>
                    <input type="password" name="password" class="form-control" id="password">
                    </div>
                    <div class="form-group last mb-4">
                        <label for="password">Bank Account Number</label>
                        <input type="password" name="password" class="form-control" id="password">
                        </div>
                  </div>
                  <input type="submit" value="Submit" class="btn btn-pill text-white btn-block btn-primary">
                

                 
                </form>
                  
    </div>
                </div>
          </div>
        </div>
      </div>
      <?php
      include("foot.php");
      ?>

<script>

</script>