<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - Students';
$message = "";

// Reset all sessions to 30
if (isset($_POST['reset_all_sessions'])) {
    mysqli_query($conn, "UPDATE students SET sessions_left = 30");
    $message = "<div class='alert alert-success'>All student sessions have been reset to 30.</div>";
}

// Delete a student
if (isset($_POST['delete_student'])) {
    $del_id = intval($_POST['delete_id']);
    mysqli_query($conn, "DELETE FROM students WHERE id = $del_id");
    $message = "<div class='alert alert-success'>Student deleted successfully.</div>";
}

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_admin.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <?php if (isset($_GET['success']) && $_GET['success'] == 'sitin'): ?>
            <div class="alert alert-success">Sit-in recorded successfully.</div>
        <?php endif; ?>

        <?= $message ?>

        <h2 class="page-title">Students Information</h2>

        <!-- Action buttons -->
        <div class="d-flex gap-2 mb-4">
            <a href="/VelosoProject/register.php?from=admin" class="btn-primary-uc">Add Student</a>
            <form method="POST" onsubmit="return confirm('Reset ALL sessions to 30?')" style="margin:0;">
                <button type="submit" name="reset_all_sessions" class="btn-danger-uc" style="padding:10px 20px;">
                    Reset All Sessions
                </button>
            </form>
        </div>

        <div class="card-uc">
            <div class="card-uc-body" style="padding:20px;">

                <!-- Search and entries control -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Show</span>
                        <select id="entries-select" class="form-control form-control-sm"
                                style="width:70px;" onchange="changeEntries(this.value)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="small text-muted">entries per page</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Search:</span>
                        <input type="text" id="table-search" class="form-control form-control-sm"
                               style="width:200px;" oninput="filterTable()" placeholder="">
                    </div>
                </div>

                <!-- Students table -->
                <div class="table-responsive">
                    <table class="table table-uc mb-0" id="students-table">
                        <thead>
                            <tr>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Email</th>
                                <th>Sessions Left</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM students ORDER BY last_name ASC");
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_number']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course']) ?></td>
                                    <td><?= htmlspecialchars($row['year_level']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $row['sessions_left'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['sessions_left'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_student.php?id=<?= $row['id'] ?>" class="btn-outline-uc me-1">Edit</a>
                                        <form method="POST" style="display:inline;"
                                              onsubmit="return confirm('Delete this student?')">
                                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_student" class="btn-danger-uc">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination info -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted" id="pagination-info"></div>
                    <div id="pagination-controls" class="d-flex gap-1"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Table filtering and pagination
var allRows        = Array.from(document.querySelectorAll('#table-body tr'));
var filteredRows   = [...allRows];
var currentPage    = 1;
var entriesPerPage = 10;

function filterTable() {
    var query = document.getElementById('table-search').value.toLowerCase();
    filteredRows = allRows.filter(function (row) {
        return row.innerText.toLowerCase().includes(query);
    });
    currentPage = 1;
    renderTable();
}

function changeEntries(val) {
    entriesPerPage = parseInt(val);
    currentPage = 1;
    renderTable();
}

function renderTable() {
    var start = (currentPage - 1) * entriesPerPage;
    var end   = start + entriesPerPage;
    var total = filteredRows.length;

    // Hide all rows then show only current page
    allRows.forEach(function (row) { row.style.display = 'none'; });
    filteredRows.slice(start, end).forEach(function (row) { row.style.display = ''; });

    // Update info text
    document.getElementById('pagination-info').textContent =
        'Showing ' + (Math.min(start + 1, total)) + ' to ' + Math.min(end, total) + ' of ' + total + ' entries';

    // Build pagination buttons
    var pages = Math.ceil(total / entriesPerPage);
    var ctrl  = document.getElementById('pagination-controls');
    ctrl.innerHTML = '';
    for (var i = 1; i <= pages; i++) {
        var btn = document.createElement('button');
        btn.textContent = i;
        btn.className   = 'btn btn-sm ' + (i === currentPage ? 'btn-primary-uc text-white' : 'btn-outline-uc');
        btn.dataset.page = i;
        btn.onclick = function () {
            currentPage = parseInt(this.dataset.page);
            renderTable();
        };
        ctrl.appendChild(btn);
    }
}

// Run on load
renderTable();
</script>

<?php require_once '../includes/footer.php'; ?>