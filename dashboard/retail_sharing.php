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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Retail Sharing</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Retail Sharing</h6>
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
      
      <h3>Retail Sharing</h3>
      <div class="row my-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
            
            
            <div class=" container home-container page-contain">
    
    <hr class="basic-hr">
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
            <br>
            <h4 class="main-color">Register Business Partner</h4>
            <p class="main-color">Your Referral Link:</p>
            <input type="text" class="form-control referral-input" id="register_invitation_link" readonly>
            <div class="row">
                <div class="col-12">
                    <br>
                    <button style="margin:10px" type="button" class="btn btn-primary" onclick="copyLink(1)">Copy link</button>
                </div>
                <!-- <div class="col-3">
                                <br>
                                <button type="button" class="btn main-btn"><i class="fas fa-share-alt"></i></button>
                            </div> -->
            </div>
            
        </div>
    </div>




    <div class="card zm-card" id="voucher_box" style="display: none;margin-top: 10px;">
        <div class="card-body">
            <div class="row">
                <div class="col-10 zm-title"><b>招募金卷</b></div>
                <div class="col-2">
                    <button type="button" class=" btn main-btn btn-circle btn-sm" onclick="open_add_voucher_modal()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div id="voucher_list"></div>
        </div>
    </div>
    <!--share url modal
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="text" class="form-control referral-input" id="referral_url" readonly>
                    <br>
                    <table>
                        <tr aria-colspan="3">Share To :</tr>
                        <tr>
                            <td style="width: 33%;"><button type="button" class="btn share-icon" onclick="show_coming_soon()"><i class="fab fa-facebook-square"></i></i></button></td>
                            <td style="width: 33%;">
                                <a class="btn share-icon" id="whatsapp_share" data-action="share/whatsapp/share"><i class="fab fa-whatsapp-square"></i></a>
                            </td>
                            <td style="width: 33%;"><button type="button" class="btn share-icon" onclick="setClipboard()"><i class="fas fa-copy"></i></button></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button onclick="closeshare2()" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>-->

    <!--add voucher modal-->
    <div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-header">
                        <table>
                            <tr>
                                <td style="width: 90%;"><b>Create Voucher</b></td>
                                <td style="width: 10%;">
                                    <button class="btn" style="color: lightslategray;" onclick="close_add_voucher_modal()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br>
                    <form method="POST" id="voucher_form">
                        <div class="mb-3">
                            <label for="Select" class="form-label">Select Package</label>
                            <select id="package_id" name="package_id" class="form-select">
                                    
                                </select>
                        </div>
                        <div class="mb-3">
                            <label for="Select" class="form-label">Package</label>
                            <select id="country_id" name="country_id" class="form-select">

                                </select>
                        </div>
                        <button type="submit" class="btn main-btn w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


            </div>
            <div class="card-body px-0 pb-2">
            <div class="flex p-2">
           
              </div>
</div>
            </div>
          </div>
        </div>
        
      <?php
      include("foot.php");
      ?>

<script>
    $(document).ready(function() {
        //get_member_info();
        $("#register_invitation_link").val(share_url + "register.html?referral=" + localStorage.username);
        $("#retail_invitation_link").val(share_url + "guest_retail_order.html?referral=" + localStorage.user_id);
        // $("#whatsapp_share").attr("href", "whatsapp://send?text=" + share_url + "register.html?referral=" + localStorage.username);
        //check_is_display_voucher();
        //get_voucher();
    });

    function download_image(url) {
        const a = document.createElement('a')
        a.href = url
        a.download = url.split('/').pop()
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
    }

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
                    if (response.data.data.register_qr != "" && response.data.data.register_qr != null && response.data.data.register_qr != "https://ainra.co/3fscp/img/register_poster/") {
                        $("#register_qr").attr("src", response.data.data.register_qr);
                        $("#register_qr_download").attr("href", response.data.data.register_qr);
                    }
                    if (response.data.data.retail_qr != "" && response.data.data.retail_qr != null && response.data.data.retail_qr != "https://ainra.co/3fscp/img/retail_poster/") {
                        $("#retail_qr").attr("src", response.data.data.retail_qr);
                        $("#retail_qr_download").attr("href", response.data.data.retail_qr);
                    }
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    function show_coming_soon() {
        warning_response("Coming Soon !");
    }

    function copyLink(type, is_close_modal = 0) {
        if (type == 1) {
            var copyText = document.getElementById("register_invitation_link");
        } else if (type == 2) {
            var copyText = document.getElementById("retail_invitation_link");
        } else {
            var copyText = document.getElementById("referral_url");
        }
        copyText.select();
        document.execCommand("copy");
        success_response_with_timer("Copied");
        if (is_close_modal == 1) {
            $('#shareModal').modal('hide');
        }
    }

    function openshare2(voucher_id) {
        var get_voucher_referral_url = new FormData();
        get_voucher_referral_url.set('api_key', api_key);
        get_voucher_referral_url.set('access_token', localStorage.access_token);
        get_voucher_referral_url.set('user_id', localStorage.user_id);
        get_voucher_referral_url.set('voucher_id', voucher_id);

        axios.post(address + 'v1/Api/get_voucher_referral_url', get_voucher_referral_url, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    $("#referral_url").val(share_url + "register.html?referral=" + localStorage.username + "&voucher=" + response.data.data.code);
                    // $('#shareModal').modal('show');
                    copyLink(3, 0);
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    function closeshare2() {
        $('#shareModal').modal('hide');

    }

    function check_is_display_voucher() {
        var check_is_display_voucher = new FormData();
        check_is_display_voucher.set('api_key', api_key);
        check_is_display_voucher.set('access_token', localStorage.access_token);
        check_is_display_voucher.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/check_is_display_voucher', check_is_display_voucher, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    if (response.data.data.is_valid == 1) {
                        $("#voucher_box").show();
                    } else {
                        $("#voucher_box").hide();
                    }
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    function get_voucher() {
        var get_voucher_list = new FormData();
        get_voucher_list.set('api_key', api_key);
        get_voucher_list.set('access_token', localStorage.access_token);
        get_voucher_list.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/get_voucher_list', get_voucher_list, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    display_voucher(response.data.data);
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    function open_add_voucher_modal() {
        var get_voucher_package = new FormData();
        get_voucher_package.set('api_key', api_key);
        get_voucher_package.set('access_token', localStorage.access_token);
        get_voucher_package.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/get_voucher_package', get_voucher_package, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    display_package(response.data.data.package);
                    display_country(response.data.data.country);
                    $('#voucherModal').modal('show');
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    }

    $('#voucher_form').submit(function(e) {
        e.preventDefault();

        var insert_voucher_package = new FormData(this);
        insert_voucher_package.set('api_key', api_key);
        insert_voucher_package.set('access_token', localStorage.access_token);
        insert_voucher_package.set('user_id', localStorage.user_id);

        axios.post(address + 'v1/Api/insert_voucher_package', insert_voucher_package, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.oauth_token
                }
            })
            .then(function(response) {
                if (response.data.status == "Success") {
                    $('#voucherModal').modal('hide');
                    success_response("Add Successfully !", true, "invite.html");
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
    });

    function display_voucher(json_response) {
        $("#voucher_list").html("");
        var voucher_list = "";
        $.each(json_response, function(i, data) {
            if (data.is_claim == 0) {
                voucher_list += '<div class="card zm-pac-card" onclick="openshare2(' + data.voucher_id + ')">';
            } else {
                voucher_list += '<div class="card zm-pac-card">';
            }
            voucher_list += '<div class="card-body">';
            voucher_list += '<input type="text" class="form-control referral-input" id="referral_url" value="' + share_url + "register.html?referral=" + localStorage.username + "&voucher=" + data.voucher_code + '" readonly><br>';

            voucher_list += '<div style="width: 100%; display: flex;">';
            voucher_list += '<div style="width: 80%;">';
            voucher_list += '<b>' + data.package_name + '</b><br><span class="zm-voucher">' + data.voucher_code + "</span><br>";
            if (data.is_claim == 0) {
                voucher_list += '<span class="zm-status-unclaimed"> Unclaimed</span>';
            } else {
                voucher_list += '<span class="zm-status-unclaimed"> Claimed</span>';
            }
            voucher_list += '</div>';
            voucher_list += '<div style="width: 20%;">';
            voucher_list += '<i class="fas fa-copy" style="font-size: 30px; margin-top: 20px;"></i>';
            voucher_list += '</div>';
            voucher_list += '</div>';

            voucher_list += '</div>';
            voucher_list += '</div>';
        });
        $("#voucher_list").append(voucher_list);
    }

    function display_package(json_response) {
        $("#package_id").html("");
        var package_list = "";
        package_list += '<option value="0">Select Package</option>';
        $.each(json_response, function(i, data) {
            package_list += '<option value="' + data.id + '">' + data.package_name + '</option>';
        });
        $("#package_id").append(package_list);
    }

    function display_country(json_response) {
        $("#country_id").html("");
        var country_list = "";
        country_list += '<option value="0">Select Country</option>';
        $.each(json_response, function(i, data) {
            country_list += '<option value="' + data.id + '">' + data.name + '</option>';
        });
        $("#country_id").append(country_list);
    }

    function close_add_voucher_modal() {
        $('#voucherModal').modal('hide');
    }
</script>