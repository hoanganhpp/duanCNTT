<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Lấy danh sách chức vụ cùng với tên nhân viên
$query = "
    SELECT 
        cv.id,            -- Thêm ID chức vụ
        cv.employee_id, 
        nv.ten AS employee_name, 
        cv.chuc_vu, 
        cv.mo_ta, 
        cv.trang_thai 
    FROM 
        bang_chuc_vu cv
    JOIN 
        bang_nhan_vien nv ON cv.employee_id = nv.id
"; 

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

include 'layout/header.php';
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Danh Sách Chức Vụ</h4>
            <button class="btn btn-success" data-toggle="modal" data-target="#addPositionModal">Thêm Chức Vụ</button>
        </div>
        <div class="card-body">
            <table id="positionTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
                        <th>Tên Nhân Viên</th>
                        <th>Tên Chức Vụ</th>
                        <th>Mô Tả</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['employee_id']; ?></td>
                            <td><?php echo $row['employee_name']; ?></td>
                            <td><?php echo $row['chuc_vu']; ?></td>
                            <td><?php echo $row['mo_ta']; ?></td>
                            <td><?php echo $row['trang_thai']; ?></td>
                            <td>
                                <button class="btn btn-warning edit-btn" 
                                        data-id="<?php echo $row['id']; ?>" 
                                        data-name="<?php echo $row['chuc_vu']; ?>" 
                                        data-description="<?php echo $row['mo_ta']; ?>" 
                                        data-status="<?php echo $row['trang_thai']; ?>" 
                                        data-toggle="modal" 
                                        data-target="#editPositionModal">Sửa</button>

                                <button class="btn btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">Xóa</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Sửa Chức Vụ -->
<div class="modal fade" id="editPositionModal" tabindex="-1" aria-labelledby="editPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPositionModalLabel">Sửa Chức Vụ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPositionForm">
                    <input type="hidden" id="editPositionId">
                    <div class="form-group">
                        <label for="editPositionName">Tên Chức Vụ</label>
                        <input type="text" class="form-control" id="editPositionName" required>
                    </div>
                    <div class="form-group">
                        <label for="editPositionDescription">Mô Tả</label>
                        <textarea class="form-control" id="editPositionDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editPositionStatus">Trạng Thái</label>
                        <select id="editPositionStatus" class="form-control">
                            <option value="hoạt động">Hoạt Động</option>
                            <option value="không hoạt động">Không Hoạt Động</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thêm chức vụ -->
<div class="modal fade" id="addPositionModal" tabindex="-1" aria-labelledby="addPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPositionModalLabel">Thêm Chức Vụ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addPositionForm">
                    <div class="form-group">
                        <label for="addEmployeeId">Employee ID</label>
                        <input type="text" class="form-control" id="addEmployeeId" required>
                    </div>
                    <div class="form-group">
                        <label for="addPositionName">Tên Chức Vụ</label>
                        <input type="text" class="form-control" id="addPositionName" required>
                    </div>
                    <div class="form-group">
                        <label for="addPositionDescription">Mô Tả</label>
                        <textarea class="form-control" id="addPositionDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addPositionStatus">Trạng Thái</label>
                        <select id="addPositionStatus" class="form-control">
                            <option value="hoạt động">Hoạt Động</option>
                            <option value="không hoạt động">Không Hoạt Động</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm Chức Vụ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Sửa chức vụ
    $('.edit-btn').click(function() {
        $('#editPositionId').val($(this).data('id'));
        $('#editPositionName').val($(this).data('name'));
        $('#editPositionDescription').val($(this).data('description'));
        $('#editPositionStatus').val($(this).data('status'));
    });

    $('#editPositionForm').on('submit', function(event) {
        event.preventDefault();
        const id = $('#editPositionId').val();
        const name = $('#editPositionName').val();
        const description = $('#editPositionDescription').val();
        const status = $('#editPositionStatus').val();

        $.ajax({
            url: 'edit_position.php',
            type: 'POST',
            data: { id: id, chuc_vu: name, mo_ta: description, trang_thai: status },
            success: function(response) {
                const result = JSON.parse(response);
                alert(result.message);
                if (result.success) {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });

    // Thêm chức vụ
    $('#addPositionForm').on('submit', function(event) {
        event.preventDefault();
        const employeeId = $('#addEmployeeId').val();
        const name = $('#addPositionName').val();
        const description = $('#addPositionDescription').val();
        const status = $('#addPositionStatus').val();

        $.ajax({
            url: 'add_position.php',
            type: 'POST',
            data: { employee_id: employeeId, chuc_vu: name, mo_ta: description, trang_thai: status },
            success: function(response) {
                const result = JSON.parse(response);
                alert(result.message);
                if (result.success) {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });

    // Xóa chức vụ
    $('.delete-btn').click(function() {
        const positionId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa chức vụ này?')) {
            $.ajax({
                url: 'delete_position.php',
                type: 'POST',
                data: { id: positionId },
                success: function(response) {
                    const result = JSON.parse(response);
                    alert(result.message);
                    if (result.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }
    });
});
</script>

<?php include 'layout/footer.php'; ?>
