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
        </div>
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="mt-3 px-3"> Welcome <span id="h-name">Ali Jan</span></span>
              <a href="../login.html" class="btn btn-light btn-sn py-2 mt-2">Log Out</a>
            </div>
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
                  <form method="post" >
                    <div class="form-group">
                    <label for="password" class="float-left">Email</label>
                    <div class="form-group" id="user_email"></div>
                    </div>
                  <div class="row">
                    <div class="form-group">
                    <label for="username" class="float-left">Username</label>
                    <div class="form-group" id="user_username"></div>
                    </div>
                    <div class="form-group">
                      <label for="fullname">Full Name</label>
                      <input type="text" name="fullname" class="form-control " id="user_fullname">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="phone_no">Phone No:</label>
                      <input type="text" name="phone_no" class="form-control col-md-4" id="user_phone_no">
                      </div>
                  

                  <input type="button" onclick="update_profile()" value="Submit" class="btn btn-pill text-white btn-block w-100 btn-primary">

                 
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
                <div id="address_list"></div>

                <div class="d-grid gap-2">
                    <button type="buttion" class="btn btn-success" onclick="oppen_add_address()">Add Address</button>
                </div>

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
              <div class="form-group first">
                <button type="button" class="btn btn-success w-100" onclick="open_change_second_password()">Change Security Code</button>
              </div>
              <div class="form-group first">
                <button type="button" class="btn btn-success w-100" onclick="open_change_password()">Change Password</button>        
              </div>
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
                    <label for="bank">Bank Name</label>
                      <select class="form-control" name="bank_name" id="bank_name">
                        <option selected>Open this select menu</option>
                        <option value="Maybank Berhad">Maybank Berhad</option>
                        <option value="CIMB Bank Berhad">CIMB Bank Berhad</option>
                        <option value="Public Bank Berhad">Public Bank Berhad</option>
                        <option value="RHB Bank Berhad">RHB Bank Berhad</option>
                        <option value="Hong Leong Bank Berhad">Hong Leong Bank Berhad</option> 
                        <option value="AmBank (M) Berhad">AmBank (M) Berhad</option>
                        <option value="UOB Malaysia">UOB Malaysia</option>
                        <option value="Bank Rakyat">Bank Rakyat</option>
                        <option value="Bank of China (Malaysia) Berhad">Bank of China (Malaysia) Berhad</option>
                        <option value="OCBC Bank (Malaysia) Berhad">OCBC Bank (Malaysia) Berhad</option>
                        <option value="HSBC Bank Malaysia Berhad">HSBC Bank Malaysia Berhad</option>
                        <option value="Bank Islam Malaysia">Bank Islam Malaysia</option>
                        <option value="Affin Bank Berhad">Affin Bank Berhad</option>
                        <option value="Alliance Bank Malaysia Berhad">Alliance Bank Malaysia Berhad</option>
                        <option value="Standard Chartered Bank Malaysia">Standard Chartered Bank Malaysia</option>
                        <option value="MBSB Bank Berhad">MBSB Bank Berhad</option>
                        <option value="Citibank Berhad">Citibank Berhad</option>
                        <option value="Banl Simpanan Nasional(BSN)">Bank Simpanan Nasional(BSN)</option>
                        <option value="Bank Muamalat Malaysia">Bank Muamalat Malaysia</option>
                        <option value="Agrobank">Agrobank</option>
                        <option value="Al-Rajhi Bank">Al-Rajhi Bank</option>
                        <option value="Co-opbank Pertama">Co-opbank Pertama</option>
                      </select>
                    </div>
                    <div class="form-group last mb-4">
                      <label for="account_name">Bank Account Holder</label>
                      <input type="text" name="account_name" class="form-control" id="account_name">
                    </div>
                    <div class="form-group last mb-4">
                        <label for="account_no">Bank Account Number</label>
                        <input type="number" name="account_no" class="form-control" id="account_no">
                        </div>
                  </div>
                  <input type="button" value="Submit" onclick="update_profile()" class="btn btn-pill text-white w-100 btn-primary">
              
                 
                </form>
                  
    </div>
                </div>
          </div>
        </div>
      </div>
      <?php
      include("foot.php");
      ?>

    <!-- new address modal -->
    <div class="modal fade" id="addressModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Address</h5>
                    <button type="button" class="btn-close" style="background-color:black" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">
                    <form method="POST" id="address_form">
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Home">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Postcode</label>
                            <input type="number" class="form-control" id="postcode" name="postcode">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editAddressModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Address</h5>
                    <button type="button" class="btn-close" style="background-color:black" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="edit_address_form">
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_address_name" name="name" placeholder="e.g. Home">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Address</label>
                            <input type="text" class="form-control" id="edit_address" name="address">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">City</label>
                            <input type="text" class="form-control" id="edit_city" name="city">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Postcode</label>
                            <input type="number" class="form-control" id="edit_postcode" name="postcode">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">State</label>
                            <input type="text" class="form-control" id="edit_state" name="state">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Submit</button>
                        <input type="hidden" id="edit_address_id" name="address_id">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="security_pin" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fill in Your Security Code</h5>
                <button type="button" class="btn-close" style="background-color:black" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

                <div class="modal-body">
                    <form class="left10px" method="POST" id="profile_form">
                        <div class="form-group">
                            <label for="exampleInputPassword1">Security Code</label>
                            <input type="password" class="form-control" id="security_code" placeholder="Security Code">
                        </div>

                        <br>
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                        <input type="hidden" name="fullname" id="edit_fullname">
                        <input type="hidden" name="phone_no" id="edit_phone_no">
                        <input type="hidden" name="bank_name" id="edit_bank_name">
                        <input type="hidden" name="account_name" id="edit_account_name">
                        <input type="hidden" name="account_no" id="edit_account_no">
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="change_password">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Change Password</h5>
                    <button type="button" class="btn-close" style="background-color:black" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="change_password_form">
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Old Password</label>
                            <input type="password" class="form-control" id="password" name="original_password">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_password">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="change_second_password">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Change Security Code</h5>
                    <button type="button" class="btn-close" style="background-color:black" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="change_security_code_form">

                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">New Security Code</label>
                            <input type="password" class="form-control" id="new_pincode" name="new_security_code">
                        </div>
                        <div class="mb-3 voucher-input">
                            <label for="input" class="form-label">Confirm Security Code</label>
                            <input type="password" class="form-control" id="confirm_new_pincode" name="confirm_security_code">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
function update_profile() {
        $("#edit_fullname").val($("#user_fullname").val());
        $("#edit_phone_no").val($("#user_phone_no").val());
        $("#edit_bank_name").val($("#bank_name").val());
        $("#edit_account_name").val($("#account_name").val());
        $("#edit_account_no").val($("#account_no").val());
        $('#security_pin').modal('show');
    }

    function oppen_add_address() {
        $('#addressModal').modal('show');
    }

    function close_add_address() {
        $('#addressModal').modal('hide')
    }

    function edit_address(address_id) {
        var update_password = new FormData();
        update_password.set('api_key', api_key);
        update_password.set('access_token', localStorage.access_token);
        update_password.set('user_id', localStorage.user_id);
        update_password.set('address_id', address_id);

        axios.post(address + 'v1/Api/get_user_address_info', update_password, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    $("#edit_address_name").val(response.data.data.name);
                    $("#edit_address").val(response.data.data.address);
                    $("#edit_city").val(response.data.data.city);
                    $("#edit_state").val(response.data.data.state);
                    $("#edit_postcode").val(response.data.data.postcode);
                    $("#edit_address_id").val(response.data.data.id);
                    $('#editAddressModal').modal('show');
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });

    }

    function close_edit_address() {
        $('#edit_address').modal('hide')
    }

    function open_change_password() {
        $('#change_password').modal('show')
    }

    function open_change_second_password() {
        $('#change_second_password').modal('show')
    }

    function go_logout() {
        window.location.href = "login.html";
    }

    $(document).ready(function() {
        get_member_info();
        get_address();
    });

    function get_member_info() {
        var get_member_info = new FormData();
        get_member_info.set('api_key', api_key);
        get_member_info.set('access_token', localStorage.access_token);
        get_member_info.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/get_member_info', get_member_info, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    $("#package").html(response.data.data.package);
                    $("#username").html(response.data.data.username);
                    $("#user_email").html(response.data.data.email);
                    $("#user_username").html(response.data.data.username);
                    $("#user_fullname").val(response.data.data.fullname);
                    $("#user_phone_no").val(response.data.data.phone_no);
                    $('#bank_name option[value="' + response.data.data.bank_name + '"]').prop('selected', true);
                    $("#account_name").val(response.data.data.account_name);
                    $("#account_no").val(response.data.data.account_no);
                    $("#profile-img").attr("src", response.data.data.profile_image);
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    $('#profile_form').submit(function(e) {
        e.preventDefault();

        var edit_profile = new FormData(this);
        edit_profile.set('api_key', api_key);
        edit_profile.set('access_token', localStorage.access_token);
        edit_profile.set('user_id', localStorage.user_id);
        edit_profile.set('security_code', $("#security_code").val());

        axios.post(address + 'v1/Api/update_profile', edit_profile, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    Swal.fire({
                        type: "success",
                        text: "Update Successfully !",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        location.reload();
                    })
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    $('#change_password_form').submit(function(e) {
        e.preventDefault();

        var update_password = new FormData(this);

        update_password.set('api_key', api_key);
        update_password.set('access_token', localStorage.access_token);
        update_password.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/update_password', update_password, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    Swal.fire({
                        type: "success",
                        text: "Update Successfully !",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location = 'login.html';
                    })
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    $('#change_security_code_form').submit(function(e) {
        e.preventDefault();

        var update_security_code = new FormData(this);
        update_security_code.set('api_key', api_key);
        update_security_code.set('access_token', localStorage.access_token);
        update_security_code.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/update_security_code', update_security_code, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    Swal.fire({
                        type: "success",
                        text: "Update Successfully !",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        location.reload();
                    })
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    function get_address() {
        var get_address = new FormData();
        get_address.set('api_key', api_key);
        get_address.set('access_token', localStorage.access_token);
        get_address.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/get_address', get_address, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    display_address(response.data.data);
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    function display_address(json_response) {
        $("#address_list").html("");
        var address_list = "";
        $.each(json_response, function(i, data) {
            address_list += '<div class="row"> <div class="col-9">';
            address_list += '<h6><b>' + data.name + '</b></h6>';
            address_list += '<p class="font-size13">' + data.address + ', ' + data.city + ', ' + data.postcode + ', ' + data.state + '</p>';
            address_list += '</div><div class="col-3"><button type="button" class="btn btn-danger me-1" onclick="delete_address(' + data.id + ')"><i class="fas fa-trash-alt"></i></button>';
            address_list += '<button type="button" class="btn btn-warning" onclick="edit_address(' + data.id + ')"><i class="fas fa-edit"></i></button>';
            address_list += '</div></div>';
            address_list += '<br>';
            address_list += '<hr>';
        });
        $("#address_list").append(address_list);
    }

    $('#address_form').submit(function(e) {
        e.preventDefault();

        var add_address = new FormData(this);
        add_address.set('api_key', api_key);
        add_address.set('access_token', localStorage.access_token);
        add_address.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/insert_address', add_address, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {

                console.log(response);
                if (response.data.status == "Success") {
                    Swal.fire({
                        type: "success",
                        text: "Add Successfully !",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        location.reload();
                    })
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    $('#edit_address_form').submit(function(e) {
        e.preventDefault();

        var edit_address = new FormData(this);
        edit_address.set('api_key', api_key);
        edit_address.set('access_token', localStorage.access_token);
        edit_address.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/update_user_address', edit_address, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    Swal.fire({
                        type: "success",
                        text: "Update Successfully !",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        location.reload();
                    })
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    function delete_address(address_id) {
        Swal.fire({
            text: "Are you sure you want to delete ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                var delete_user_address = new FormData();
                delete_user_address.set('api_key', api_key);
                delete_user_address.set('access_token', localStorage.access_token);
                delete_user_address.set('user_id', localStorage.user_id);
                delete_user_address.set('address_id', address_id);

                axios.post(address + 'v1/Api/delete_user_address', delete_user_address, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Authorization': localStorage.oauth_token
                        }
                    })
                    .then(function(response) {
                        if (response.data.status == "Success") {
                            Swal.fire({
                                type: "success",
                                text: "Delete Successfully !",
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            })
                        } else {
                            warning_response(response.data.message);
                        }
                    })
                    .catch(function(data) {
                        console.log(data);
                        error_response();
                    });
            }
        })
    }
</script>