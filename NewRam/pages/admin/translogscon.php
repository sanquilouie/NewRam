<?php
session_start();
include '../../includes/connection.php';


if (!isset($_SESSION['email']) || ($_SESSION['role'] != 'Cashier' && $_SESSION['role'] != 'Superadmin' && $_SESSION['role'] != 'Admin')) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Logs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
</head>

<body>
<?php
        include '../../includes/topbar.php';
        include '../../includes/sidebar2.php';
        include '../../includes/footer.php';
    ?>
    <div id="main-content" class="container-fluid mt-5 <?php echo ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Cashier') ? '' : 'sidebar-expanded'; ?>" class="container-fluid mt-5">
        <h2>Remittance Logs</h2>
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-10 col-lg-8 col-xl-8 col-xxl-8">
                <div class="table-responsive">
                <div class="input-group mb-3">
                    <input type="text" id="busSearch" class="form-control" placeholder="Search Bus No">
                </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bus No</th>
                                <th>Conductor ID</th>
                                <th>Total Load</th>
                                <th>Total Cash</th>
                                <th>Total Card</th>
                                <th>Total Deductions</th>
                                <th>Total Net Amount</th>
                                <th>Remit Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="remitLogsTableBody"></tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
    <script>
        $('#busDropdownBtn').click(function () {
    $.ajax({
        url: '../../actions/get_bus_numbers.php', // your PHP endpoint
        method: 'GET',
        dataType: 'json',
        success: function (buses) {
            if (!Array.isArray(buses) || buses.length === 0) {
                Swal.fire('No buses found', '', 'info');
                return;
            }

            const options = buses.map(bus => 
                `<option value="${bus}">${bus}</option>`).join('');

            Swal.fire({
                title: 'Select Bus Number',
                html: `<select id="swal-bus-dropdown" class="form-control form-select">${options}</select>`,
                confirmButtonText: 'Generate Excel',
                focusConfirm: false,
                preConfirm: () => {
                    const selected = document.getElementById('swal-bus-dropdown').value;
                    // Redirect to the PHP script that generates the Excel file
                    window.location.href = `../../actions/generate_excel.php?bus_number=${selected}`;
                }
            });
        },
        error: function () {
            Swal.fire('Error', 'Could not fetch bus numbers', 'error');
        }
    });
});

        document.getElementById('busSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#remitLogsTableBody tr');

            rows.forEach(row => {
                const busNo = row.cells[1]?.textContent.toLowerCase(); // Column 2: Bus No
                row.style.display = busNo.includes(query) ? '' : 'none';
            });
        });
    $(document).ready(function () {
        window.generateExcel = function(busNumber, remitDate) {
            window.location.href = `../../actions/generate_excel.php?bus_number=${busNumber}&remit_date=${remitDate}`;
        };


        function loadRemitLogs(page = 1) {
            $.ajax({
                url: '../../actions/fetch_remitlogs_admin.php', // Update this to your correct PHP file
                type: 'GET',
                data: { page: page },
                dataType: 'json',
                success: function (response) {
                    let remitLogs = response.remit_logs;
                    let totalPages = response.totalPages;
                    let currentPage = response.currentPage;
                    let tableBody = $("#remitLogsTableBody"); // Update to match your table ID
                    let pagination = $("#pagination");

                    tableBody.empty();
                    pagination.empty();

                    // Populate the remit logs table
                    remitLogs.forEach(log => {
                        const remitDate = encodeURIComponent(log.remit_date);
                        const busNo = encodeURIComponent(log.bus_no);

                        tableBody.append(`
                            <tr>
                                <td>${log.remit_id}</td>
                                <td>${log.bus_no}</td>
                                <td>${log.conductor_id}</td>
                                <td>${parseFloat(log.total_load).toFixed(2)}</td>
                                <td>${parseFloat(log.total_cash).toFixed(2)}</td>
                                <td>${parseFloat(log.total_card).toFixed(2)}</td>
                                <td>${parseFloat(log.total_deductions).toFixed(2)}</td>
                                <td>${parseFloat(log.total_net_amount).toFixed(2)}</td>
                                <td>${log.remit_date}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="generateExcel('${busNo}', '${remitDate}')">
                                        Generate Excel
                                    </button>
                                </td>
                            </tr>

                        `);
                    });


                    // Responsive pagination logic
                    function addPageButton(pageNumber, isActive = false) {
                        pagination.append(`
                            <li class="page-item ${isActive ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${pageNumber}">${pageNumber}</a>
                            </li>
                        `);
                    }

                    function addEllipsis() {
                        pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }

                    // Previous button
                    if (currentPage > 1) {
                        pagination.append(`
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                            </li>
                        `);
                    }

                    let screenWidth = $(window).width();
                    let showAll = screenWidth > 768; // Show all pages on larger screens

                    if (showAll) {
                        // Full pagination
                        for (let i = 1; i <= totalPages; i++) {
                            addPageButton(i, currentPage === i);
                        }
                    } else {
                        // Compact pagination
                        if (currentPage > 2) addPageButton(1); // First page
                        if (currentPage > 3) addEllipsis();

                        let start = Math.max(1, currentPage - 1);
                        let end = Math.min(totalPages, currentPage + 1);

                        for (let i = start; i <= end; i++) {
                            addPageButton(i, currentPage === i);
                        }

                        if (currentPage < totalPages - 2) addEllipsis();
                        if (currentPage < totalPages - 1) addPageButton(totalPages); // Last page
                    }

                    // Next button
                    if (currentPage < totalPages) {
                        pagination.append(`
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                            </li>
                        `);
                    }

                    // Dropdown for mobile users
                    if (screenWidth < 576) {
                        let selectDropdown = `<select id="pageSelect" class="form-select form-select-sm">`;
                        for (let i = 1; i <= totalPages; i++) {
                            selectDropdown += `<option value="${i}" ${i === currentPage ? "selected" : ""}>Page ${i}</option>`;
                        }
                        selectDropdown += `</select>`;
                        pagination.append(`<li class="page-item">${selectDropdown}</li>`);
                    }
                }
            });
        }

        // Initial load
        loadRemitLogs();

        // Handle pagination click
        $(document).on("click", ".page-link", function (e) {
            e.preventDefault();
            let page = $(this).data("page");
            loadRemitLogs(page);
        });

        // Handle dropdown change (for mobile)
        $(document).on("change", "#pageSelect", function () {
            let page = $(this).val();
            loadRemitLogs(page);
        });

        // Re-render pagination on window resize
        $(window).resize(function () {
            loadRemitLogs($(".page-item.active .page-link").data("page") || 1);
        });
    });

        </script>
</body>

</html>