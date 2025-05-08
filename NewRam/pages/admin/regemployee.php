<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../libraries/PHPMailer/src/PHPMailer.php';
require '../../libraries/PHPMailer/src/SMTP.php';
require '../../libraries/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../includes/connection.php';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values and sanitize inputs
    $employeeType = $_POST['employeeType'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['contactnumber'];
    $birthday = $_POST['dob'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $accountNumber = $_POST['employeeNumber'];


    if (isset($_POST['update_id']) && isset($_POST['new_status'])) {
        $update_id = mysqli_real_escape_string($conn, $_POST['update_id']);
        $new_status = (int) $_POST['new_status'];

        $update_sql = "UPDATE useracc SET is_activated = $new_status WHERE account_number = '$update_id'";
        mysqli_query($conn, $update_sql);

        // Optional: redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
        exit;
    }

    // Calculate age
    $age = date_diff(date_create($birthday), date_create('today'))->y;

    // Password generation
    $pass = 'ramstarbus123';
    $password = md5($pass); // Consider using password_hash() for better security

    // Set default role and activation status
    $role = $employeeType;
    $activated = 1;

    // Prepare the SQL query
    $query = "INSERT INTO useracc (
        account_number, firstname, middlename, lastname, email, contactnumber, birthday, age, gender,
        province, municipality, barangay, address, password, role, is_activated
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("sssssssssssssssi", $accountNumber, $firstName, $middleName, $lastName, $email, 
            $phone, $birthday, $age, $gender, $province, $municipality, $barangay, $address, $password, $role, $activated);

        // Execute the query
        if ($stmt->execute()) {

            $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ramstarzaragoza@gmail.com';
                    $mail->Password = 'hwotyendfdsazoar'; // App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
    
                    $mail->setFrom('ramstarzaragoza@gmail.com', 'Ramstar Bus Transportation');
                    $mail->addAddress($email, $firstName . ' ' . $lastName);
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Received';
                    $mail->Body = "
                        <p>Dear $firstName,</p>
                        <p>Congratulations! You are officially hired for the position of $role at our company.</p>
                        <p>We look forward to wor;king with you and are excited to have you on the team.</p>
                        <p><strong>Account Number:</strong>  $accountNumber
                        <br><strong>Default Password:</strong> ramstarbus123</p>
                        <p>To get started, please log in to our company portal using the link below:</p>
                        <p>https://ramstarzaragosa.site/</p>
                        <p>Best regards,
                        <br>Ramstar Bus Transportation</p>
                    ";
    
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
                }

            $_SESSION['success'] = 'Employee registered successfully!';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['error'] = 'Error during registration!';
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Error preparing the SQL query!';
    }

    // Close connection
    $conn->close();
}


ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebars.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Use full version -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
 
    
    <style>
      .country-code {
            background-color: #f8f9fa;
            /* Light background */
            border-right: 1px solid #ced4da;
            /* Border to separate from input */
            display: flex;
            align-items: center;
            /* Center vertically */
            padding: 0.5rem;
            /* Padding around the text */
            font-weight: bold;
            /* Bold text for emphasis */
        }

        /* Contact number input field */
        #phone {
            padding-left: 0.5rem;
            /* Padding to align text with country code */
        }
    </style>
    
</head>
<body>
    <?php
        include '../../includes/topbar.php';
        include '../../includes/sidebar2.php';
        include '../../includes/footer.php';
    ?>
<div id="main-content" class="container-fluid mt-5 <?php echo ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Cashier') ? '' : 'sidebar-expanded'; ?>" class="container-fluid mt-5">
    <div class="row justify-content-center">
        <h2>Employee List</h2>
        <div class="col-12 col-sm-10 col-md-10 col-lg-8 col-xl-8 col-xxl-8">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#employeeModal">
            + Register Employee
            </button>

            <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title w-100 text-center" id="employeeModalLabel">Employee Registration</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                    <label for="employeeType" class="form-label required">Employee Type<span class="text-danger">*</span></label>
                                        <select class="form-select" id="employeeType" name="employeeType" required>
                                            <option value="" disabled selected>Select Role</option>
                                            <option value="Conductor">Conductor</option>
                                            <option value="Driver">Driver</option>
                                            <option value="Cashier">Cashier</option>
                                            <option value="Inspector">Inspector</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employeeNumber" class="form-label required">Employee No.<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="employeeNumber" name="employeeNumber" placeholder="Scan NFC Here" required>
                                        <div id="employeeNumberFeedback" class="invalid-feedback"></div>
                                    </div>

                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="firstName" class="form-label">First Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Enter first name" required pattern="[A-Za-z\s]+" title="Letters only">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="middleName" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Enter Middle name" pattern="[A-Za-z\s]+" title="Letters only">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lastName" class="form-label">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Enter last name" required pattern="[A-Za-z\s]+" title="Letters only">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                                        <div id="emailFeedback" class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone<span class="text-danger">*</span></label>
                                        <div class="form-group position-relative">
                                            <input type="text" class="form-control ps-5" id="phone" name="contactnumber" placeholder="" required pattern="\d{10}" maxlength="10" required/>
                                            <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted">+63</span>
                                        </div>
                                        <div id="contactError" class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    
                                <div class="col-md-6">
                                        <label for="dob" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="dob" name="dob">
                                </div>
                                <div class="col-md-6">   
                                        <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                </div>
                                </div>
                                <div class="row mb-3">   
                                    <div class="col-md-12">
                                        <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Purok#/Street/Sitio" required> 
                                    </div>
                                </div>
                                <div class="row mb-3">         
                                    <div class="col-md-4">
                                        <label for="province" class="form-label">Province<span class="text-danger">*</span></label>
                                        <select class="form-select" id="province" name="province" required>
                                            <option value="">-- Select Province --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="municipality" class="form-label">Municipality<span class="text-danger">*</span></label>
                                        <select class="form-select" id="municipality" name="municipality" required> 
                                            <option value="">-- Select Municipality --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="barangay" class="form-label">Barangay<span class="text-danger">*</span></label>
                                        <select class="form-select" id="barangay" name="barangay" required>
                                            <option value="">-- Select Barangay --</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Conditional fields for Driver -->
                                <div id="driverFields" class="driver-fields" style="display:none;">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                                <label for="driverLicense" class="form-label required">Driver's License No.</label>
                                                <input type="text" class="form-control" id="driverLicense" name="driverLicense" placeholder="Enter license number">
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional fields for Conductor -->
                                <div id="conductorFields" class="conductor-fields" style="display:none;">
                                    <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="workExperience" class="form-label required">Work Experience</label>
                                            <textarea class="form-control" id="workExperience" rows="2" placeholder="Enter work experience"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional fields for Cashier -->
                                <div id="cashierFields" class="cashier-fields" style="display:none;">
                                    <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="cashHandlingExperience" class="form-label required">Cash Handling Experience</label>
                                            <textarea class="form-control" id="cashHandlingExperience" rows="2" placeholder="Enter experience in cash handling"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional fields for Inspector -->
                                <div id="inspectorFields" class="inspector-fields" style="display:none;">
                                    <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="cashHandlingExperience" class="form-label required">Cash Handling Experience</label>
                                            <textarea class="form-control" id="cashHandlingExperience" rows="2" placeholder="Enter experience in cash handling"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3"> 
                <form method="GET">
                    <select name="role_filter" class="form-select" onchange="this.form.submit()" style="width: 150px;">
                        <option value="">All Roles</option>
                        <option value="Driver" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'Driver' ? 'selected' : '' ?>>Driver</option>
                        <option value="Conductor" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'Conductor' ? 'selected' : '' ?>>Conductor</option>
                        <option value="Cashier" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                        <option value="Inspector" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'Inspector' ? 'selected' : '' ?>>Inspector</option>
                    </select>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Account #</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Date Hired</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $allowed_roles = ['Driver', 'Conductor', 'Cashier', 'Inspector'];
                        $filter = isset($_GET['role_filter']) ? $_GET['role_filter'] : 'all';

                        $where = "WHERE role IN ('Driver', 'Conductor', 'Cashier', 'Inspector')";
                        if (in_array($filter, $allowed_roles)) {
                            $where = "WHERE role = '" . mysqli_real_escape_string($conn, $filter) . "'";
                        }

                        $limit = 10;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $start = ($page - 1) * $limit;

                        $sql = "SELECT account_number, firstname, middlename, lastname, suffix, email, contactnumber, created_at, role, is_activated 
                        FROM useracc
                        $where 
                        ORDER BY created_at DESC 
                        LIMIT $start, $limit";

                        $result = mysqli_query($conn, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
                                if (!empty($row['suffix'])) {
                                    $fullname .= ', ' . $row['suffix'];
                                }
                                $action_label = $row['is_activated'] == 1 ? 'Disable' : 'Enable';
                                $action_status = $row['is_activated'] == 1 ? 2 : 1;
                                echo "<tr>
                                        <td>{$row['account_number']}</td>
                                        <td>" . htmlspecialchars($fullname) . "</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['contactnumber']}</td>
                                        <td>{$row['role']}</td>
                                        <td>" . date('F d, Y', strtotime($row['created_at'])) . "</td>
                                        <td>
                                            <form method='post' class='status-form' data-label='$action_label' style='display:inline-block;'>
                                                <input type='hidden' name='update_id' value='{$row['account_number']}'>
                                                <input type='hidden' name='new_status' value='{$action_status}'>
                                                <button type='button' class='btn btn-sm btn-" . ($action_status == 1 ? "success" : "danger") . " confirm-status'>$action_label</button>
                                            </form>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No records found.</td></tr>";
                        }

                        // PAGINATION
                        $count_sql = "SELECT COUNT(*) AS total FROM useracc $where";
                        $count_result = mysqli_query($conn, $count_sql);
                        $total = mysqli_fetch_assoc($count_result)['total'];
                        $pages = ceil($total / $limit);

                        ?>
                    </tbody>
                </table>
                <?php if ($pages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&role_filter=<?= urlencode($filter) ?>" tabindex="-1">Prev</a>
                        </li>

                        <!-- Page Numbers -->
                        <?php 
                        // Display a range of pages around the current one
                        $range = 2; // Number of pages before and after the current page to display
                        $start = max(1, $page - $range);
                        $end = min($pages, $page + $range);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&role_filter=<?= urlencode($filter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= $page == $pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&role_filter=<?= urlencode($filter) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>    
    </div>
</div>


<script src="../../assets/js/address_api.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
const capitalize = (input) => {
        let value = input.value.trim();
        if (value.length > 0) {
            input.value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        }
    };

    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const middleName = document.getElementById('middleName');

    firstName.addEventListener('blur', () => capitalize(firstName));
    lastName.addEventListener('blur', () => capitalize(lastName));
    middleName.addEventListener('blur', () => capitalize(middleName));
});

document.querySelectorAll('.confirm-status').forEach(button => {
    button.addEventListener('click', function () {
        const form = this.closest('form');
        const label = form.getAttribute('data-label');

        Swal.fire({
            title: `Are you sure you want to ${label.toLowerCase()} this user?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, ${label}`,
            cancelButtonText: 'Cancel',
            confirmButtonColor: label === 'Disable' ? '#d33' : '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

<?php if (isset($_SESSION['success'])): ?>
    Swal.fire({
        title: 'Registration Successful!',
        text: '<?php echo $_SESSION['success']; ?>',
        icon: 'success',
        confirmButtonColor: '#3085d6'
    });
<?php unset($_SESSION['success']); endif; ?>


document.querySelectorAll('#firstName, #middleName, #lastName').forEach(input => {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z\s]/g, '');
    });
});


const licenseInput = document.getElementById("driverLicense");

licenseInput.addEventListener("input", function(e) {
  // Remove hyphens only
  let raw = e.target.value.replace(/-/g, "");

  // Limit to 11 characters total
  raw = raw.substring(0, 11);

  // Format: XXX-XX-XXXXX
  let formatted = '';
  if (raw.length > 0) {
    formatted += raw.substring(0, 3);
  }
  if (raw.length > 3) {
    formatted += '-' + raw.substring(3, 5);
  }
  if (raw.length > 5) {
    formatted += '-' + raw.substring(5, 11);
  }

  e.target.value = formatted;
});

function checkSubmitButton() {
    var emailValid = !$('#email').hasClass('is-invalid');
    var contactValid = !$('#phone').hasClass('is-invalid');
    var empNumValid = !$('#employeeNumber').hasClass('is-invalid');

    // Enable the register button only if both are valid
    var registerButton = $('.primary');
    if (emailValid && contactValid && empNumValid) {
        registerButton.prop('disabled', false);
    } else {
        registerButton.prop('disabled', true);
    }
}

$(document).ready(function () {
    
    var today = new Date();
    today.setFullYear(today.getFullYear() - 18);  // Subtract 18 years
    document.getElementById("dob").setAttribute("max", today.toISOString().split('T')[0]);


  let registerButton = $('.btn-primary');
  $('#phone').on('input', function () {
    var contactValue = $(this).val().replace(/[^0-9]/g, '').substring(0, 11);
    $(this).val(contactValue);

    // Make sure to disable the button initially
    var registerButton = $('.primary');
    registerButton.prop('disabled', true);

    if (contactValue.length === 11) {
        $.ajax({
            type: "POST",
            url: "../../actions/check_contact.php", // Make sure this file checks contact number
            data: { contactnumber: contactValue },
            dataType: "json",
            success: function (response) {
                if (response.exists) {
                    $('#phone').addClass('is-invalid');
                    $('#contactError').text("This contact number is already registered.").show();
                } else {
                    $('#phone').removeClass('is-invalid');
                    $('#contactError').hide();
                }
                checkSubmitButton(); // Check if both email and contact are valid
            },
            error: function () {
                console.error("Error checking contact number.");
            }
        });
    } else {
        $('#phone').removeClass('is-invalid');
        $('#contactError').hide();
        checkSubmitButton(); // Re-check submit status
    }
});

    $('#email').on('input', function () {
        var email = $(this).val();
        var registerButton = $('.primary');
        registerButton.prop('disabled', true); // Disable register button initially

        if (email) {
            $.ajax({
                url: '../../actions/check_email.php', // File to check email availability
                type: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function (response) {
                    if (response.exists) {
                        $('#email').addClass('is-invalid');
                        $('#emailFeedback').remove();
                        $('#email').after('<div id="emailFeedback" class="invalid-feedback">This email is already registered.</div>');
                    } else {
                        $('#email').removeClass('is-invalid');
                        $('#emailFeedback').remove();
                    }
                    checkSubmitButton(); // Check if both email and contact are valid
                },
                error: function () {
                    console.error('Error checking email.');
                }
            });
        } else {
            $('#email').removeClass('is-invalid');
            $('#emailFeedback').remove();
            checkSubmitButton(); // Re-check submit status
        }
    });


    $('#employeeNumber').on('input', function () {
        var empNo = $(this).val();
        var registerButton = $('.primary');
        registerButton.prop('disabled', true); // Disable button while checking

        if (empNo) {
            $.ajax({
                url: '../../actions/check_employee_no.php',
                type: 'POST',
                data: { employeeNumber: empNo },
                dataType: 'json',
                success: function (response) {
                    if (response.exists) {
                        $('#employeeNumber').addClass('is-invalid');
                        $('#employeeNumberFeedback').remove();
                        $('#employeeNumber').after('<div id="employeeNumberFeedback" class="invalid-feedback">This Employee Number is already registered.</div>');
                    } else {
                        $('#employeeNumber').removeClass('is-invalid');
                        $('#employeeNumberFeedback').remove();
                    }
                    checkSubmitButton(); // Optionally validate form again
                },
                error: function () {
                    console.error('Error checking Employee Number.');
                }
            });
        } else {
            $('#employeeNumber').removeClass('is-invalid');
            $('#employeeNumberFeedback').remove();
            checkSubmitButton();
        }
    });

$(document).ready(function () {
    let confirmationShown = false;

    const form = $("form")[0];

    $('.btn-primary').click(function (event) {
        event.preventDefault();

        // Extra custom validation
        if ($('#email').hasClass('is-invalid')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'This Email is already registered. Please use another.',
            });
            return;
        }

        if ($('#employeeNumber').hasClass('is-invalid')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Employee Number',
                text: 'This Employee Number is already registered. Please use another.',
            });
            return;
        }

        if (!confirmationShown) {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            confirmationShown = true;

            Swal.fire({
                title: 'Confirm Registration?',
                text: "Are you sure you want to register?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#cc0000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, register!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Registering...',
                        html: 'Please wait while we process your registration.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            form.submit();  // Actual submission here, backend does the emailing
                        }
                    });
                } else {
                    confirmationShown = false; // Allow another try if canceled
                }
            });
        }
    });
});


    // JavaScript to show/hide additional fields based on selected role
    document.getElementById("employeeType").addEventListener("change", function() {
        var role = this.value;
        var employeeNumberField = document.getElementById("employeeNumber");
        
        // Hide all fields first
        document.getElementById("driverFields").style.display = "none";
        document.getElementById("conductorFields").style.display = "none";
        document.getElementById("cashierFields").style.display = "none";
        document.getElementById("inspectorFields").style.display = "none";
        
        // Enable/disable the employee number field and show specific fields based on role
        if (role === "Driver") {
            document.getElementById("driverFields").style.display = "block";
            employeeNumberField.readOnly = false;
            employeeNumberField.placeholder = "Scan NFC Here";   
            employeeNumberField.value = "";  
        } else if (role === "Conductor") {
            //document.getElementById("conductorFields").style.display = "block";
            employeeNumberField.readOnly = false;
            employeeNumberField.placeholder = "Scan NFC Here";   
            employeeNumberField.value = ""; 
        } else if (role === "Cashier") {
            //document.getElementById("cashierFields").style.display = "block";
            employeeNumberField.readOnly = false;
            employeeNumberField.placeholder = "Scan NFC Here";   
            employeeNumberField.value = "";   
        } else if (role === "Inspector") {
            //document.getElementById("cashierFields").style.display = "block";
            employeeNumberField.readOnly = false;
            employeeNumberField.placeholder = "Scan NFC Here";   
            employeeNumberField.value = "";   
        }
    });
});
</script>
</body>
</html>