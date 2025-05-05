<?php
session_start();
include '../../includes/connection.php';

if (!isset($_SESSION['email']) || ($_SESSION['role'] != 'User')) {
    header("Location: ../../index.php");
    exit();
} ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebars.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .password-wrapper {
            position: relative;
        }

        .password-wrapper i {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .indicator {
            display: none;
            margin-bottom: 10px;
        }

        .indicator span {
            display: inline-block;
            width: 80px;
            height: 10px;
            background: #e0e0e0;
            margin-right: 5px;
            border-radius: 5px;
        }

        .indicator .active {
            background: red;
        }

        .indicator .medium.active {
            background: orange;
        }

        .indicator .strong.active {
            background: green;
        }
    </style>
</head>

<body>
<?php
    include '../../includes/topbar.php';
    include '../../includes/sidebar2.php';
    include '../../includes/footer.php';
?>

<div id="main-content" class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-10 col-lg-8 col-xl-8 col-xxl-8">
            <h2>Update Password</h2>
            <form id="updatePasswordForm" onsubmit="updatePassword(event)">
                <label for="pass2" class="form-label">Old Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" name="old_pass" id="pass2" placeholder="Old Password" required>
                    <i class="fa-solid fa-eye" onclick="togglePassword('pass2', this)"></i>
                </div>

                <label for="pass1" class="form-label">New Password</label>
                <div class="password-wrapper">
                    <input onkeyup="trigger()" type="password" class="form-control" name="Password" id="pass1" placeholder="New Password" required>
                    <i class="fa-solid fa-eye" onclick="togglePassword('pass1', this)"></i>
                </div>

                <div class="indicator">
                    <span class="weak"></span>
                    <span class="medium"></span>
                    <span class="strong"></span>
                </div>

                <label for="pass3" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control mb-2" name="PasswordConf" id="pass3" placeholder="Confirm Password" required>
                    <i class="fa-solid fa-eye" onclick="togglePassword('pass3', this)"></i>
                </div>

                <div id="matchMsg" class="mb-2"></div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-block">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    const input = document.querySelector("#pass1");
    const weak = document.querySelector(".weak");
    const medium = document.querySelector(".medium");
    const strong = document.querySelector(".strong");
    const indicator = document.querySelector(".indicator");

    function trigger() {
        const password = input.value;
        indicator.style.display = password.length ? "block" : "none";

        weak.classList.remove("active");
        medium.classList.remove("active");
        strong.classList.remove("active");

        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[\W]/)) strength++;

        if (strength <= 1) {
            weak.classList.add("active");
        } else if (strength === 2 || strength === 3) {
            medium.classList.add("active");
        } else if (strength >= 4) {
            strong.classList.add("active");
        }
    }

    const pass1 = document.getElementById("pass1");
    const pass3 = document.getElementById("pass3");
    const matchMsg = document.getElementById("matchMsg");

    pass1.addEventListener("input", checkMatch);
    pass3.addEventListener("input", checkMatch);

    function checkMatch() {
        if (pass3.value === "") {
            matchMsg.textContent = "";
        } else if (pass1.value === pass3.value) {
            matchMsg.textContent = "Passwords match";
            matchMsg.style.color = "green";
        } else {
            matchMsg.textContent = "Passwords do not match";
            matchMsg.style.color = "red";
        }
    }

    function updatePassword(event) {
        event.preventDefault();

        const old_pass = document.getElementById("pass2").value;
        const Password = document.getElementById("pass1").value;
        const PasswordConf = document.getElementById("pass3").value;

        if (Password !== PasswordConf) {
            Swal.fire({
                icon: 'error',
                title: 'Mismatch',
                text: 'Passwords do not match.'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to update your password?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../../actions/update_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        old_pass: old_pass,
                        Password: Password,
                        PasswordConf: PasswordConf
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Updated!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location = '/NewRam/pages/user/update_pass.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating the password. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }
</script>


<body>
<?php
        include '../../includes/topbar.php';
        include '../../includes/sidebar2.php';
        include '../../includes/footer.php';
    ?>
    <div id="main-content" class="container-fluid mt-5 <?php echo ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Cashier') ? '' : 'sidebar-expanded'; ?>" class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-10 col-lg-8 col-xl-8 col-xxl-8">
                <h2>Update Password</h2>
                    <form id="updatePasswordForm" onsubmit="updatePassword(event)">
                    <label for="pass2" class="form-label">Old Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" name="old_pass" id="pass2" placeholder="Old Password" required>
                        <i class="fa-solid fa-eye" onclick="togglePassword('pass2', this)"></i>
                    </div>
                    <label for="pass1" class="form-label">New Password</label>
                    <div class="password-wrapper">
                        <input onkeyup="trigger()" type="password" class="form-control" name="Password" id="pass1" placeholder="New Password" required>
                        <i class="fa-solid fa-eye" onclick="togglePassword('pass1', this)"></i>
                    </div>
                        <div class="indicator">
                            <span class="weak"></span>
                            <span class="medium"></span>
                            <span class="strong"></span>
                        </div>
                        <label for="pass3" class="form-label">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control mb-2" name="PasswordConf" id="pass3" placeholder="Confirm Password" required>
                            <i class="fa-solid fa-eye" onclick="togglePassword('pass3', this)"></i>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-block">Update Password</button>
                        </div>
                    </form>
                    </div>
        </div>
    </div>

    <script type="text/javascript">
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
            } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
            }
        }

        const indicator = document.querySelector(".indicator");
        const input = document.querySelector("#pass1");
        const weak = document.querySelector(".weak");
        const medium = document.querySelector(".medium");
        const strong = document.querySelector(".strong");

        function trigger() {
            const password = input.value;
            indicator.style.display = "block";

            let no = 0;

            if (password.length < 8) {
                no = 1;
            }
            if (password.match(/[A-z]/)) {
                no++;
            }
            if (password.match(/[0-9]/)) {
                no++;
            }
            if (password.match(/[\W]/)) {
                no++;
            }

            weak.classList.remove("active");
            medium.classList.remove("active");
            strong.classList.remove("active");

            if (no == 1) {
                weak.classList.add("active");
            } else if (no == 2) {
                medium.classList.add("active");
            } else if (no == 3) {
                strong.classList.add("active");
            }
        }
    </script>
</body>
</html>
