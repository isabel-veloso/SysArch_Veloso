<?php
// =============================================
// navbar_admin.php
// Handles: search modal + sit-in form modal
// =============================================

$search_error  = "";
$found_student = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_id'])) {
    $search_id = trim($_POST['search_id']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id_number = ?");
    mysqli_stmt_bind_param($stmt, "s", $search_id);
    mysqli_stmt_execute($stmt);
    $result        = mysqli_stmt_get_result($stmt);
    $found_student = mysqli_fetch_assoc($result);

    if (!$found_student) {
        $search_error = "No student found with ID: " . htmlspecialchars($search_id);
    } else {
        // Check if student is already sitting in
        $chk = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM sit_in_records WHERE student_id = ? AND time_out IS NULL");
        mysqli_stmt_bind_param($chk, "i", $found_student['id']);
        mysqli_stmt_execute($chk);
        $chk_row = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));

        if ($chk_row['cnt'] > 0) {
            $search_error  = "Student is already sitting in.";
            $found_student = null; // block sit-in modal from opening
        }
    }
}
?>

<!-- ============ STYLES ============ -->
<style>
    .modal-overlay        { display: none; }
    .modal-overlay.active { display: flex; }
</style>

<!-- ============ SCRIPT (must be before nav so openSearchModal is defined) ============ -->
<script>
function openSearchModal() {
    document.getElementById('search-overlay').classList.add('active');
    setTimeout(() => document.getElementById('search-input-field').focus(), 100);
}

function closeSearchModal() {
    document.getElementById('search-overlay').classList.remove('active');
}

function closeSitinModal() {
    const sitin = document.getElementById('sitin-overlay');
    if (sitin) sitin.classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function () {
    // Close search modal on overlay background click
    const searchOverlay = document.getElementById('search-overlay');
    if (searchOverlay) {
        searchOverlay.addEventListener('click', function (e) {
            if (e.target === this) closeSearchModal();
        });
    }

    // Close sit-in modal on overlay background click
    const sitinOverlay = document.getElementById('sitin-overlay');
    if (sitinOverlay) {
        sitinOverlay.addEventListener('click', function (e) {
            if (e.target === this) closeSitinModal();
        });
    }

    // Escape key closes both
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSearchModal();
            closeSitinModal();
        }
    });
});
</script>

<!-- ============ NAVBAR ============ -->
<nav class="navbar navbar-expand-lg navbar-uc">
    <div class="container-fluid px-4">
        <span class="navbar-brand-text">College of Computer Studies Sit-in Monitoring System</span>
        <div class="navbar-links">
            <a href="/VelosoProject/admin/admindashboard.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'admindashboard.php' ? 'active' : '' ?>">
                Dashboard
            </a>
            <a href="#" class="nav-link-uc" onclick="openSearchModal(); return false;">Search</a>
            <a href="/VelosoProject/admin/students.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>">
                Students
            </a>
            <a href="/VelosoProject/admin/current_sitin.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'current_sitin.php' ? 'active' : '' ?>">
                Current Sit-in
            </a>
            <a href="/VelosoProject/admin/sitin_records.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'sitin_records.php' ? 'active' : '' ?>">
                Sit-in Records
            </a>
            <a href="/VelosoProject/admin/feedbacks.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'feedbacks.php' ? 'active' : '' ?>">
                Feedbacks
            </a>
            <a href="/VelosoProject/logout.php" class="nav-link-uc nav-link-logout">Logout</a>
        </div>
    </div>
</nav>


<!-- ============ SEARCH MODAL ============ -->
<div class="modal-overlay" id="search-overlay">
    <div class="modal-box">
        <div class="modal-box-header">
            <span>Search Student</span>
            <button class="modal-close" onclick="closeSearchModal()">&#x2715;</button>
        </div>
        <div class="modal-box-body">

            <?php if ($search_error): ?>
                <div class="alert alert-danger"><?= $search_error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="open_search" value="1">
                <input
                    type="text"
                    name="search_id"
                    id="search-input-field"
                    class="form-control mb-3"
                    placeholder="Enter student ID number"
                    value="<?= htmlspecialchars($_POST['search_id'] ?? '') ?>"
                >
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-primary-uc">Search</button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ============ SIT-IN FORM MODAL ============ -->
<?php if ($found_student): ?>
<div class="modal-overlay active" id="sitin-overlay">
    <div class="modal-box">
        <div class="modal-box-header">
            <span>Sit-In Form</span>
            <button class="modal-close" onclick="closeSitinModal()">&#x2715;</button>
        </div>
        <div class="modal-box-body">
            <form method="POST" action="/VelosoProject/process_sitin.php">

                <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="hidden" name="student_id"  value="<?= $found_student['id'] ?>">

                <div class="modal-field">
                    <label>ID Number</label>
                    <input type="text" class="form-control readonly-input"
                           value="<?= htmlspecialchars($found_student['id_number']) ?>" readonly>
                </div>

                <div class="modal-field">
                    <label>Student Name</label>
                    <input type="text" class="form-control readonly-input"
                           value="<?= htmlspecialchars($found_student['first_name'] . ' ' . $found_student['last_name']) ?>" readonly>
                </div>

                <div class="modal-field">
                    <label>Purpose</label>
                    <select name="purpose" class="form-control" required>
                        <option value="" disabled selected>Select purpose</option>
                        <option value="C Programming">C Programming</option>
                        <option value="Java">Java</option>
                        <option value="Systems Analysis">Systems Analysis</option>
                        <option value="Database">Database</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Research">Research</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Lab</label>
                    <select name="lab" class="form-control" required>
                        <option value="" disabled selected>Select lab</option>
                        <option value="524">524</option>
                        <option value="526">526</option>
                        <option value="528">528</option>
                        <option value="530">530</option>
                        <option value="542">542</option>
                        <option value="544">544</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Sessions Left</label>
                    <input type="text"
                           class="form-control readonly-input <?= $found_student['sessions_left'] <= 0 ? 'text-danger fw-bold' : '' ?>"
                           value="<?= $found_student['sessions_left'] ?>" readonly>
                </div>

                <?php if ($found_student['sessions_left'] <= 0): ?>
                    <div class="alert alert-danger">This student has no remaining sessions.</div>
                <?php endif; ?>

                <div class="modal-actions">
                    <button type="button" class="btn-outline-uc" onclick="closeSitinModal()">Close</button>
                    <button type="submit" class="btn-primary-uc"
                            <?= $found_student['sessions_left'] <= 0 ? 'disabled' : '' ?>>
                        Sit In
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php elseif (isset($_POST['search_id']) || isset($_POST['open_search'])): ?>
<script>
    // Re-open search modal if search returned no result
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('search-overlay').classList.add('active');
        document.getElementById('search-input-field').focus();
    });
</script>
<?php endif; ?>