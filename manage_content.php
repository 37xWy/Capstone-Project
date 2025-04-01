<?php
include 'admin_auth.php';
include 'db.php';

$content_type = isset($_GET['type']) ? $_GET['type'] : 'diet';

$contentConfig = [
    'diet' => [
        'query' => "SELECT * FROM diet_plans",
        'columns' => ['diet_id', 'plan_name', 'description', 'calories', 'diet_type'],
        'form_fields' => '
            <input type="text" name="plan_name" placeholder="Plan Name" required>
            <textarea name="description" placeholder="Plan Description" required></textarea>
            <input type="number" name="calories" placeholder="Daily Calories" required min="0">
            <select name="diet_type" required>
                <option value="keto">Keto</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="high_protein">High Protein</option>
            </select>
        ',
    ],
    'exercise' => [
        'query' => "SELECT * FROM exercise",
        'columns' => ['E_id', 'E_name', 'E_category', 'E_target', 'E_difficulty'],
        'form_fields' => '
            <input type="text" name="exercise_name" placeholder="Exercise Name" required>
            <select name="category" required>
                <option value="Cardiovascular">Cardiovascular</option>
                <option value="Strength">Strength</option>
                <option value="Flexibility">Flexibility</option>
            </select>
            <input type="text" name="target" placeholder="Target Muscle Group" required>
            <input type="number" name="difficulty" placeholder="Difficulty" required min="1" max="10">
        ',
    ],
    'workout' => [
        'query' => "SELECT * FROM workout_plans",
        'columns' => ['plan_id', 'plan_name', 'description', 'days', 'weeks', 'difficulty'],
        'form_fields' => '
            <input type="text" name="plan_name" placeholder="Plan Name" required>
            <textarea name="description" placeholder="Plan Description" required></textarea>
            <input type="number" name="days" placeholder="Number of Days" required min="1" max="7">
            <input type="number" name="weeks" placeholder="Number of Weeks" required min="1">
            <select name="difficulty" required>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
        ',
    ],
    'assignment' => [
        'query' => null,
        'columns' => [],
        'form_fields' => ''
    ],
];

$content = [];
$form_fields = '';
$columns = [];

if ($content_type !== 'assignment') {
    $query = $contentConfig[$content_type]['query'];
    $result = $conn->query($query);
    $columns = $contentConfig[$content_type]['columns'];
    $form_fields = $contentConfig[$content_type]['form_fields'];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $content[] = $row;
        }
    }
} else {
    $daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    
    $plansResult = $conn->query("SELECT * FROM workout_plans");
    $exerciseResult = $conn->query("SELECT * FROM exercise");
    
    $form_fields .= '<label for="plan_id">Select Workout Plan:</label> ';
    $form_fields .= '<select name="plan_id" id="plan_id">';
    while ($plan = $plansResult->fetch_assoc()) {
        $form_fields .= '<option value="'.$plan["plan_id"].'" data-days="'.$plan["days"].'">'
            .$plan["plan_name"].' ('.$plan["days"].' days)</option>';
    }
    $form_fields .= '</select>';
    
    $allExercises = [];
    $categories = [];
    while ($ex = $exerciseResult->fetch_assoc()) {
        $allExercises[] = $ex;
        if (!in_array($ex["E_category"], $categories)) {
            $categories[] = $ex["E_category"];
        }
    }
    
    $form_fields .= '<div id="assignmentFilters" style="margin: 10px 0;">';
    $form_fields .= '<input type="text" id="exerciseNameFilter" placeholder="Search Exercises..." style="margin-right:10px;">';
    $form_fields .= '<select id="exerciseTypeFilter">';
    $form_fields .= '<option value="">All Types</option>';
    foreach ($categories as $cat) {
        $form_fields .= '<option value="'.htmlspecialchars($cat).'">'.htmlspecialchars($cat).'</option>';
    }
    $form_fields .= '</select>';
    $form_fields .= '</div>';
    
    $form_fields .= '<div style="overflow-x:hidden; margin-top:10px;">';
    $form_fields .= '<table class="assignment-table" id="assignmentTable">';
    $form_fields .= '<thead><tr>';
    $form_fields .= '<th>Exercise</th>';
    foreach ($daysOfWeek as $day) {
        $abbr = substr($day, 0, 3);
        $form_fields .= '<th>'.$abbr.'</th>';
    }
    $form_fields .= '</tr></thead>';
    $form_fields .= '<tbody>';
    foreach ($allExercises as $ex) {
        $form_fields .= '<tr data-exercise-id="'.$ex["E_id"].'" data-exercise-type="'.htmlspecialchars($ex["E_category"]).'">';
        $form_fields .= '<td>'.$ex["E_name"].'</td>';
        foreach ($daysOfWeek as $day) {
            $form_fields .= '<td><input type="checkbox" class="day-checkbox" data-day="'.$day.'" /></td>';
        }
        $form_fields .= '</tr>';
    }
    $form_fields .= '</tbody></table>';
    $form_fields .= '</div>';
    
    $form_fields .= '<input type="hidden" name="assignments" id="assignmentsInput">';
}
$conn->close();

function renderContentContainer($content_type, $content, $columns, $form_fields) {
    ob_start(); ?>
    <div class="container" id="contentContainer">
      <h2>Content Management</h2>
      <p class="subtext">Manage system content including Diet Plans, Exercises, Workout Plans, and Plan Assignments</p>
      
      <div class="controls">
        <select id="contentType">
          <option value="manage_content.php?type=diet" <?= $content_type == 'diet' ? 'selected' : '' ?>>Diet Plans</option>
          <option value="manage_content.php?type=exercise" <?= $content_type == 'exercise' ? 'selected' : '' ?>>Exercises</option>
          <option value="manage_content.php?type=workout" <?= $content_type == 'workout' ? 'selected' : '' ?>>Workout Plans</option>
          <option value="manage_content.php?type=assignment" <?= $content_type == 'assignment' ? 'selected' : '' ?>>Plan Assignments</option>
        </select>
        <?php if ($content_type !== 'assignment'): ?>
          <input type="text" id="contentSearch" placeholder="Search <?= ucfirst($content_type) ?>...">
        <?php endif; ?>
      </div>
      
      <?php if ($content_type == 'assignment'): ?>
        <div class="add-form">
          <h3><i class="fas fa-link"></i> Assign Exercises to a Workout Plan</h3>
          <form id="assignmentForm" method="POST" action="assignment_handler.php">
            <?= $form_fields ?>
            <button type="submit" id="saveAssignmentBtn"><i class="fas fa-save"></i> Save Assignment</button>
          </form>
        </div>
      <?php else: ?>
        <div class="add-form">
          <h3><i class="fas fa-plus-circle"></i> Add New <?= ucfirst($content_type) ?></h3>
          <form method="POST" action="manage_content_handler.php?type=<?= $content_type ?>" id="dataForm">
            <?= $form_fields ?>
            <button type="submit"><i class="fas fa-save"></i> Save <?= ucfirst($content_type) ?></button>
          </form>
        </div>
        
        <table class="data-table" id="contentTable">
          <thead>
            <tr>
              <?php foreach ($columns as $col): ?>
                <th><?= ucfirst(str_replace('_', ' ', $col)) ?></th>
              <?php endforeach; ?>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="contentTableBody">
            <?php if (count($content) > 0): ?>
              <?php foreach ($content as $item): ?>
                <tr>
                  <?php foreach ($columns as $col): ?>
                    <td><?= htmlspecialchars($item[$col]) ?></td>
                  <?php endforeach; ?>
                  <td class="actions-cell">
                    <button class="btn btn-edit" onclick="editItem('<?= $content_type ?>', '<?= $item[$columns[0]] ?>')">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-delete" onclick="deleteItem('<?= $content_type ?>', '<?= $item[$columns[0]] ?>')">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="<?= count($columns)+1 ?>">No records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    echo renderContentContainer($content_type, $content, $columns, $form_fields);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Content Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="style-wy.css">
</head>
<body>
  <div class="wrapper">
    <?php include 'header_sidebar.php'; ?>
    <?= renderContentContainer($content_type, $content, $columns, $form_fields); ?>
  </div>
  
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close">&times;</span>
      <h3>Edit <span id="editType"></span></h3>
      <form id="editForm">
        <div id="editFields"></div>
        <input type="hidden" name="id" id="editId">
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>
  
  <script>
    function initContent() {
      const searchInput = document.getElementById("contentSearch");
      if (searchInput) {
        searchInput.addEventListener("input", function() {
          const filter = this.value.toLowerCase();
          const rows = document.querySelectorAll("#contentTableBody tr");
          rows.forEach(row => {
            let match = false;
            for (let cell of row.cells) {
              if (cell.textContent.toLowerCase().includes(filter)) {
                match = true;
                break;
              }
            }
            row.style.display = match ? "" : "none";
          });
        });
      }
      
      document.getElementById("contentType").addEventListener("change", function() {
        loadContent(this.value);
      });
      
      const dataForm = document.getElementById("dataForm");
      if (dataForm) {
        dataForm.addEventListener("submit", function(e) {
          e.preventDefault();
          const form = this;
          const formData = new FormData(form);
          fetch(form.action, { method: 'POST', body: formData })
          .then(response => response.text())
          .then(result => {
            console.log("Save result:", result);
            loadContent("manage_content.php?type=" + encodeURIComponent(getContentTypeFromURL()));
            form.reset();
          })
          .catch(error => console.error("Error saving data:", error));
        });
      }
      
      if (document.getElementById("assignmentForm")) {
        initAssignmentCheckboxes();
      }
    }
    
    function getContentTypeFromURL() {
      const params = new URLSearchParams(window.location.search);
      return params.get('type') || 'diet';
    }
    
    function loadContent(url) {
      url += (url.indexOf('?') === -1 ? '?ajax=1' : '&ajax=1');
      fetch(url)
      .then(response => response.text())
      .then(html => {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newContent = tempDiv.querySelector('#contentContainer').innerHTML;
        document.getElementById('contentContainer').innerHTML = newContent;
        initContent();
        history.pushState(null, '', url.replace('&ajax=1', ''));
      })
      .catch(error => console.error('Error loading content:', error));
    }
    
    function deleteItem(type, id) {
      if (confirm(`Are you sure you want to delete this ${type}?`)) {
        fetch(`manage_content_handler.php?type=${type}&action=delete`, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ id: id })
        })
        .then(response => {
          if(response.ok) {
            loadContent(`manage_content.php?type=${type}`);
          } else {
            console.error("Deletion failed.");
          }
        })
        .catch(error => console.error("Error during deletion:", error));
      }
    }
    
    function editItem(type, id) {
      fetch(`get_record.php?type=${type}&id=${encodeURIComponent(id)}`)
      .then(response => response.json())
      .then(data => {
        populateEditModal(type, data);
        document.getElementById("editModal").style.display = "block";
      })
      .catch(error => console.error("Error fetching record data:", error));
    }
    
    function populateEditModal(type, data) {
      document.getElementById("editType").innerText = type.charAt(0).toUpperCase() + type.slice(1);
      document.getElementById("editId").value = data.id;
      let fieldsHTML = "";
      
      if (type === "diet") {
        fieldsHTML += '<input type="text" name="plan_name" value="'+ (data.plan_name || '') +'" required>';
        fieldsHTML += '<textarea name="description" required>'+ (data.description || '') +'</textarea>';
        fieldsHTML += '<input type="number" name="calories" value="'+ (data.calories || '') +'" required min="0">';
        fieldsHTML += '<select name="diet_type" required>\
                        <option value="keto" '+ (data.diet_type === 'keto' ? 'selected' : '') +'>Keto</option>\
                        <option value="vegetarian" '+ (data.diet_type === 'vegetarian' ? 'selected' : '') +'>Vegetarian</option>\
                        <option value="high_protein" '+ (data.diet_type === 'high_protein' ? 'selected' : '') +'>High Protein</option>\
                      </select>';
      } else if (type === "exercise") {
        fieldsHTML += '<input type="text" name="exercise_name" value="'+ (data.E_name || '') +'" required>';
        fieldsHTML += '<select name="category" required>\
                        <option value="Cardiovascular" '+ (data.E_category === 'Cardiovascular' ? 'selected' : '') +'>Cardiovascular</option>\
                        <option value="Strength" '+ (data.E_category === 'Strength' ? 'selected' : '') +'>Strength</option>\
                        <option value="Flexibility" '+ (data.E_category === 'Flexibility' ? 'selected' : '') +'>Flexibility</option>\
                      </select>';
        fieldsHTML += '<input type="text" name="target" value="'+ (data.E_target || '') +'" required>';
        fieldsHTML += '<input type="number" name="difficulty" value="'+ (data.E_difficulty || '') +'" required min="1" max="10">';
      } else if (type === "workout") {
        fieldsHTML += '<input type="text" name="plan_name" value="'+ (data.plan_name || '') +'" required>';
        fieldsHTML += '<textarea name="description" required>'+ (data.description || '') +'</textarea>';
        fieldsHTML += '<input type="number" name="days" value="'+ (data.days || '') +'" required min="1" max="7">';
        fieldsHTML += '<input type="number" name="weeks" value="'+ (data.weeks || '') +'" required min="1">';
        fieldsHTML += '<select name="difficulty" required>\
                        <option value="Beginner" '+ (data.difficulty === 'Beginner' ? 'selected' : '') +'>Beginner</option>\
                        <option value="Intermediate" '+ (data.difficulty === 'Intermediate' ? 'selected' : '') +'>Intermediate</option>\
                        <option value="Advanced" '+ (data.difficulty === 'Advanced' ? 'selected' : '') +'>Advanced</option>\
                      </select>';
      }
      
      document.getElementById("editFields").innerHTML = fieldsHTML;
    }
    
    document.getElementById("closeModal").onclick = function() {
      document.getElementById("editModal").style.display = "none";
    }
    
    document.getElementById("editForm").addEventListener("submit", function(e) {
      e.preventDefault();
      const form = this;
      const formData = new FormData(form);
      const type = document.getElementById("editType").innerText.toLowerCase();
      fetch(`update_content_handler.php?type=${type}`, {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(result => {
        alert(result);
        document.getElementById("editModal").style.display = "none";
        loadContent("manage_content.php?type=" + type);
      })
      .catch(error => console.error("Error updating record:", error));
    });
    
    function initAssignmentCheckboxes() {
      initAssignmentFilters();
      
      const planSelect = document.getElementById('plan_id');
      if (planSelect) {
        planSelect.addEventListener('change', function() {
          loadExistingAssignments(this.value);
        });
        loadExistingAssignments(planSelect.value);
      }
      
      document.getElementById("assignmentForm").addEventListener("submit", function(e) {
        e.preventDefault();
        let distinctDays = {};
        document.querySelectorAll("#assignmentTable tbody tr").forEach(tr => {
          tr.querySelectorAll("input.day-checkbox:checked").forEach(cb => {
            distinctDays[cb.getAttribute("data-day")] = true;
          });
        });
        const distinctCount = Object.keys(distinctDays).length;
        const allowed = 4;
        if (distinctCount > allowed) {
          alert("You can only assign exercises for up to " + allowed + " distinct day(s).");
          const planSelect = document.getElementById('plan_id');
          if(planSelect) {
             loadExistingAssignments(planSelect.value);
          }
          return;
        }
        
        const days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        let finalAssignments = {};
        days.forEach(d => { finalAssignments[d] = []; });
        
        document.querySelectorAll("#assignmentTable tbody tr").forEach(tr => {
          const eid = tr.getAttribute("data-exercise-id");
          tr.querySelectorAll("input.day-checkbox").forEach(cb => {
            if (cb.checked) {
              const dayName = cb.getAttribute("data-day");
              finalAssignments[dayName].push(eid);
            }
          });
        });
        
        console.log("Gathered final assignments:", finalAssignments);
        const jsonString = JSON.stringify(finalAssignments);
        document.getElementById("assignmentsInput").value = jsonString;
        console.log("Final assignments JSON:", jsonString);
        
        const formData = new FormData(this);
        fetch(this.action, { method: 'POST', body: formData })
        .then(res => res.text())
        .then(result => {
          alert("Server response: " + result);
          loadContent("manage_content.php?type=assignment");
        })
        .catch(err => console.error("Error saving assignments:", err));
      });
    }
    
    function loadExistingAssignments(plan_id) {
      fetch('get_assignment.php?plan_id=' + encodeURIComponent(plan_id))
      .then(res => res.json())
      .then(data => {
        console.log("Loaded existing assignments for plan", plan_id, data);
        document.querySelectorAll("#assignmentTable tbody tr").forEach(tr => {
          tr.querySelectorAll("input.day-checkbox").forEach(cb => {
            cb.checked = false;
          });
        });
        for (let day in data) {
          const eids = data[day];
          eids.forEach(eid => {
            const row = document.querySelector(`#assignmentTable tbody tr[data-exercise-id="${eid}"]`);
            if (row) {
              const checkbox = row.querySelector(`input.day-checkbox[data-day="${day}"]`);
              if (checkbox) {
                checkbox.checked = true;
              }
            }
          });
        }
      })
      .catch(err => console.error("Error loading assignments:", err));
    }
    
    function initAssignmentFilters() {
      const nameFilter = document.getElementById("exerciseNameFilter");
      const typeFilter = document.getElementById("exerciseTypeFilter");
      const rows = document.querySelectorAll("#assignmentTable tbody tr");
      
      function filterTable() {
        const searchText = (nameFilter.value || "").toLowerCase();
        const selectedType = typeFilter.value;
        rows.forEach(row => {
          const exName = row.cells[0].textContent.toLowerCase();
          const exType = row.getAttribute("data-exercise-type");
          const matchesName = exName.includes(searchText);
          const matchesType = !selectedType || exType === selectedType;
          row.style.display = (matchesName && matchesType) ? "" : "none";
        });
      }
      
      if (nameFilter) {
        nameFilter.addEventListener("input", filterTable);
      }
      if (typeFilter) {
        typeFilter.addEventListener("change", filterTable);
      }
    }
    
    document.addEventListener("DOMContentLoaded", initContent);
  </script>
</body>
</html>
