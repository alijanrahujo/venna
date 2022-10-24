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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Stock Record</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Stock Record</h6>
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
                        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
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
                        <img src="../assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
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
      <div class="row">
      <div class="col-lg-8"><h3>Stock Record</h3></div>
      <div class="col-lg-4"><a href="shipment_history.php" class="btn btn-success">View Shipment History</a></div>
      </div>
      <div class="container-fluid py-4">
        <div class="col-lg-12">
          <div class="card">
            <div class="flex p-2">
        <div class="table-responsive">
  <table class="table">
  <thead>
    <tr>
        <th>Order Id</th>
        <th>Shipment Order</th>
        <th>Date & time</th>
        <th>Bonus</th>
        <th>Status</th>

    </tr>
  </thead>
  <tbody>
        <tr>
          <td>2022-07-29 21:43:44</td>
          <td>#0003696</td>
          <td>fitty5001 (fitty5001)</td>
          <td>-1</td>
          <td>Approved</td>
        </tr>
  </tbody>
  </table>
  <br>

</div>
                 
</div></div></div>
</div>
          </div>
        </div>
        
      </div>
      <?php
      include("foot.php");
      ?>

<script>
      var restock_quantity = [];

$(document).ready(function() {
    get_restock_package();
    get_company_info();
});

function get_restock_package() {
    var get_restock_package = new FormData();
    get_restock_package.set('api_key', api_key);
    get_restock_package.set('access_token', localStorage.access_token);
    get_restock_package.set('user_id', localStorage.user_id);

    axios.post(address + 'v1/Api/get_restock_package', get_restock_package, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': localStorage.oauth_token
            }
        })
        .then(function(response) {
            if (response.data.status == "Success") {
                display_restock_package(response.data.data);
                restock_quantity = response.data.data;
                if (restock_quantity == "") {
                    $("#restock_quantity").prop("disabled", true);
                    $('#restock_quantity').empty().append($('<option value="1">').text("No Quantity Found"));
                } else {
                    $("#restock_quantity").prop("disabled", false);
                    $('#restock_quantity').empty().append($('<option value="1">').text("Select Quantity"));
                    for (var i = 0; i < restock_quantity.length; i++) {
                        var str = restock_quantity[i].display_quantity;
                        if (str.search("<br>") == 3 || str.search("<br>") == 4) {
                            var quantity = str.replace("<br>", " ");
                        } else {
                            var quantity = restock_quantity[i].display_quantity;
                        }
                        $('#restock_quantity').append($('<option value="' + restock_quantity[i].quantity_data + '">').text(quantity));
                    }
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

function get_restock_grand_total(data) {
    var quantity = $("#restock_quantity").val();
    if (typeof quantity == "undefined" || quantity == "") {
        quantity = 1;
    } else {
        quantity = data.value;
    }

    var calculate_restock_package = new FormData();
    calculate_restock_package.set('api_key', api_key);
    calculate_restock_package.set('access_token', localStorage.access_token);
    calculate_restock_package.set('user_id', localStorage.user_id);
    calculate_restock_package.set('quantity', quantity);

    axios.post(address + 'v1/Api/calculate_restock_package', calculate_restock_package, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': localStorage.oauth_token
            }
        })
        .then(function(response) {
            if (response.data.status == "Success") {
                if (response.data.data.is_available == 1) {
                    $("#package_name").html(response.data.data.package_name);
                    $("#package_quantity").html(response.data.data.package_quantity);
                    $("#package_price").html(response.data.data.package_price);
                    $("#package_subtotal").html(response.data.data.package_subtotal);
                    get_company_info(response.data.data.package_id);
                    $("#repeat_order_box").css("display", "block");
                } else {
                    $("#repeat_order_box").css("display", "none");
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

function get_company_info(package_id) {
    var get_company_info = new FormData();
    get_company_info.set('api_key', api_key);
    get_company_info.set('access_token', localStorage.access_token);
    get_company_info.set('user_id', localStorage.user_id);
    get_company_info.set('package_id', package_id);

    axios.post(address + 'v1/Api/get_company_info', get_company_info, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': localStorage.oauth_token
            }
        })
        .then(function(response) {
            if (response.data.status == "Success") {
                $("#account_name").html(response.data.data.account_name);
                $("#account_no").html(response.data.data.account_no);
                $("#bank_name").html(response.data.data.bank_name);
            } else {
                warning_response(response.data.message);
            }
        })
        .catch(function(data) {
            console.log(data);
            error_response();
        });
}

$('#restock_form').submit(function(e) {

    e.preventDefault();

    var insert_restock_package = new FormData(this);
    insert_restock_package.set('api_key', api_key);
    insert_restock_package.set('access_token', localStorage.access_token);
    insert_restock_package.set('user_id', localStorage.user_id);

    axios.post(address + 'v1/Api/insert_restock_package', insert_restock_package, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': localStorage.oauth_token
            }
        })
        .then(function(response) {

            console.log(response);

            if (response.data.status == "Success") {
                success_response("Submit Successfully !", true, "order_tracking.php");
                document.getElementById("btn-disable").disabled = true;
            } else {
                warning_response(response.data.message);
            }
        })
        .catch(function(data) {
            console.log(data);
            error_response();
        });
});

function display_restock_package(json_response) {
    var tableBody = $("#restock_package_list");
    tableBody.empty();
    var restock_package_list = ""
    $.each(json_response, function(i, data) {
        restock_package_list += '<tr class="tr_repeat_order">';
        restock_package_list += '<td class="td-50 main-color"><b>' + data.english_name + '</b></td>';
        restock_package_list += '<td class="td-25 main-color">' + data.display_quantity + '</td>';
        restock_package_list += '<td class="td-25 main-color">' + data.unit_price + '</td>';
        restock_package_list += '</tr>';
    });
    tableBody.append(restock_package_list);
}
</script>