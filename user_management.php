<?php
include 'admin_auth.php';
include 'db.php';

$sql = "SELECT 
            user_id, 
            full_name, 
            email, 
            role,
            status AS account_status
        FROM users";
$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()){
    $users[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .container {
      flex: 1;
      padding: 20px;
      background-color: #f8f9fa;
      overflow-y: auto;
    }
    .search-bar {
      margin: 20px 0;
      text-align: right;
    }
    .search-bar input {
      padding: 10px 15px;
      width: 100%;
      max-width: 300px;
      border: 1px solid #ddd;
      border-radius: 25px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    .search-bar input:focus {
      outline: none;
      border-color: #1abc9c;
      box-shadow: 0 0 8px rgba(26, 188, 156, 0.3);
    }
    .table-wrapper {
      margin-top: 20px;
      overflow-x: auto;
      overflow-y: auto;
      max-height: 415px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border-radius: 8px;
      background: white;
    }
    .table-wrapper table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }
    table th {
      background-color: #1abc9c;
      color: white;
      font-weight: 600;
      padding: 15px;
    }
    table td {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
      color: #444;
    }
    .status-active {
      color: #2ecc71;
      font-weight: 600;
      padding: 4px 8px;
      border-radius: 4px;
      background-color: #e8f8f3;
    }
    .status-deactivated {
      color: #e74c3c;
      font-weight: 600;
      padding: 4px 8px;
      border-radius: 4px;
      background-color: #fdedec;
    }
    .actions-cell {
      display: flex;
      gap: 8px;
      justify-content: center;
    }
    .action-btn {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 0.9rem;
      min-width: 140px;
    }
    .btn-active {
      background-color: #1abc9c;
      color: white;
    }
    .btn-active:hover {
      background-color: #16a085;
    }
    .btn-inactive {
      background-color: #95a5a6;
      color: white;
    }
    .btn-inactive:hover {
      background-color: #7f8c8d;
    }
    tr:hover td {
      background-color: #f8f9fa;
    }
    .role-dropdown {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
      background-color: #fff;
      color: #444;
      transition: all 0.3s ease;
    }
    .role-dropdown:focus {
      outline: none;
      border-color: #1abc9c;
      box-shadow: 0 0 8px rgba(26, 188, 156, 0.3);
    }
    .role-dropdown:hover {
      border-color: #1abc9c;
    }
    @media (max-width: 768px) {
      .container { padding: 15px; }
      .search-bar input { max-width: 100%; }
      .action-btn { min-width: 120px; padding: 6px 12px; }
      table th, table td { padding: 10px 12px; }
    }
    @media (max-width: 480px) {
      .actions-cell { flex-direction: column; gap: 4px; }
      .action-btn { width: 100%; min-width: auto; }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
      <h2>User Management</h2>
      <p>Manage user details including role and account status.</p>
      
      <div class="search-bar">
        <input type="text" id="userSearch" placeholder="Search users...">
      </div>
      
      <div class="table-wrapper">
        <table id="userTable">
          <thead>
            <tr>
              <th>Full Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Account Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): 
                $userId = htmlspecialchars($user['user_id']);
                $fullName = htmlspecialchars($user['full_name']);
                $email = htmlspecialchars($user['email']);
                $role = htmlspecialchars($user['role']);
                $accountStatus = ($user['account_status'] == 1) ? "Active" : "Deactivated";
                $toggleText = ($user['account_status'] == 1) ? "Deactivate Account" : "Re-activate Account";
            ?>
            <tr id="row_<?= $userId ?>">
              <td><?= $fullName ?></td>
              <td><?= $email ?></td>
              <td>
                <select class="role-dropdown" onchange="updateRole('<?= $userId ?>', this.value)">
                  <option value="new" <?= $role === 'new' ? 'selected' : '' ?>>New</option>
                  <option value="long_time" <?= $role === 'long_time' ? 'selected' : '' ?>>Long Time</option>
                </select>
              </td>
              <td id="accStatus_<?= $userId ?>" class="status-<?= strtolower($accountStatus) ?>">
                <?= $accountStatus ?>
              </td>
              <td class="actions-cell">
                <button class="action-btn <?= ($user['account_status'] == 1) ? 'btn-inactive' : 'btn-active' ?>"
                        onclick="toggleStatus('<?= $userId ?>')">
                  <?= $toggleText ?>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("userSearch").addEventListener("input", function() {
      const filter = this.value.toLowerCase();
      document.querySelectorAll("#userTable tbody tr").forEach(row => {
        const match = row.cells[0].textContent.toLowerCase().includes(filter) || 
                      row.cells[1].textContent.toLowerCase().includes(filter);
        row.style.display = match ? "" : "none";
      });
    });

    function updateRole(userId, newRole) {
      fetch("assign_roles.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ user_id: userId, role: newRole })
      }).then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
      }).catch(error => console.error('Error:', error));
    }

    function toggleStatus(userId) {
      const statusElem = document.getElementById(`accStatus_${userId}`);
      const button = document.querySelector(`#row_${userId} .action-btn`);
      const newStatus = statusElem.classList.contains('status-active') ? 0 : 1;

      fetch("manage_users.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ user_id: userId, status: newStatus })
      }).then(response => {
        if (response.ok) {
          statusElem.className = newStatus ? 'status-active' : 'status-deactivated';
          statusElem.textContent = newStatus ? 'Active' : 'Deactivated';
          button.textContent = newStatus ? 'Deactivate Account' : 'Re-activate Account';
          button.className = `action-btn ${newStatus ? 'btn-inactive' : 'btn-active'}`;
        }
      }).catch(error => console.error('Error:', error));
    }
  </script>
</body>
</html>