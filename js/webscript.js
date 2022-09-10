function success_response(data, redirect = false, redirect_page) {
    if (redirect) {
        Swal.fire({
            type: "success",
            text: data,
            confirmButtonText: 'OK'
        }).then((result) => {
            localStorage.route = redirect_page;
            window.location.href = redirect_page;
            //$("#content").html("");
            //$("#content").load(redirect_page);
        })
    } else {
        Swal.fire({
            type: "success",
            text: data,
            confirmButtonText: 'OK'
        })
    }
}
function success_response_with_timer(data, redirect = false, redirect_page) {
    if (redirect) {
        Swal.fire({
            type: "success",
            text: data,
            showConfirmButton: false,
            timer: 1000,
        }).then((result) => {
            localStorage.route = redirect_page;
            window.location.href = redirect_page;
            //$("#content").html("");
            //$("#content").load(redirect_page);
        })
    } else {
        Swal.fire({
            type: "success",
            text: data,
            showConfirmButton: false,
            timer: 1000,
        })
    }
}

function warning_response_order(data) {
    Swal.fire({
        type: "warning",
        text: data,
        confirmButtonText: 'OK'
    }).then((result) => {
        localStorage.route = "my_order.html";
        window.location.href = "my_order.html";
        //$("#content").html("");
        //$("#content").load("my_order.html");
    })
}

function warning_response(data) {
    if (data == "Invalid Token") {
        Swal.fire({
            type: "warning",
            text: "Token Expiry, Please Login Again !",
            confirmButtonText: 'OK'
        }).then((result) => {
            window.location.href = "../../login.html";
        })
    } else {
        Swal.fire({
            type: "warning",
            text: data,
            confirmButtonText: 'OK'
        })
    }
}

function error_response() {
    Swal.fire({
        type: "error",
        text: 'Something Error !',
        confirmButtonText: 'OK'
    })
}



function check_user_status() {
    var get_user_information = new FormData();
    get_user_information.set('api_key', api_key);
    get_user_information.set('access_token', localStorage.access_token);
    get_user_information.set('user_id', localStorage.user_id);

    axios.post(address + 'v1/Basic_Api/get_user_information', get_user_information, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': localStorage.oauth_token
            }
        })
        .then(function(response) {
            if (response.data.status == "Success") {
                if (response.data.data.is_step1 == 0) {
                    window.location.href = "register_package.html";
                } else if (response.data.data.is_step2 == 0) {
                    window.location.href = "register_package2.html";
                } else if (response.data.data.is_step3 == 0) {
                    window.location.href = "register_package3.html";
                } else if (response.data.data.is_step4 == 0) {
                    window.location.href = "register_package4.html";
                } else if (response.data.data.is_done == 0) {
                    window.location.href = "register_package_review.html";
                } else {
                    if (typeof localStorage.user_id == "undefined" || localStorage.user_id == "") {
                        localStorage.username = response.data.data.username;
                        window.location.href = "login.html";
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


// function get_member_info() {
    
//     var get_member_info = new FormData();
//     get_member_info.set('api_key', api_key);
//     get_member_info.set('access_token', localStorage.access_token);
//     get_member_info.set('user_id', localStorage.user_id);

//     axios.post(address + 'v1/Api/get_member_info', get_member_info, {
//             headers: {
//                 'Content-Type': 'multipart/form-data',
//                 'Authorization': localStorage.oauth_token
//             }
//         })
//         .then(function(response) {
//             if (response.data.status == "Success") {
//                 // $("#package").html(response.data.data.package);
//                 // $("#username").html(response.data.data.username);
//                 // $("#user_email").html(response.data.data.email);
//                 // $("#user_username").html(response.data.data.username);
//                 // $("#user_fullname").val(response.data.data.fullname);
//                 // $("#user_phone_no").val(response.data.data.phone_no);
//                 // $('#bank_name option[value="' + response.data.data.bank_name + '"]').prop('selected', true);
//                 // $("#account_name").val(response.data.data.account_name);
//                 // $("#account_no").val(response.data.data.account_no);
//                 //$("#profile-img").attr("src", response.data.data.profile_image);
//             } else {
//                 warning_response(response.data.message);
//             }
//         })
//         .catch(function(data) {
//             console.log(data);
//             error_response();
//         });
// }



async function get_member_info() {

    var get_member_info = new FormData();
    get_member_info.set('api_key', api_key);
    get_member_info.set('access_token', localStorage.access_token);
    get_member_info.set('user_id', localStorage.user_id);

    return await axios.post(address + 'v1/Api/get_member_info', get_member_info, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': localStorage.oauth_token
        }
    });
}

(async () => {
    var user = await get_member_info();
    console.log(user.data.data.company_type);
})()
