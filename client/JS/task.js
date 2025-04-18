document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();
  
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const due_date = document.getElementById('due_date').value;
  
    fetch('https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/add_task.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: "include",
      body: JSON.stringify({ title, description, due_date })
    })
    .then(response => response.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        loadTasks();
        document.getElementById('taskForm').reset();
      }
    })
    .catch(error => {
      alert('Error: ' + error.message);
    });
  });
  
  function loadTasks() {
    fetch('https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/get_tasks.php', {
      credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('taskContainer');
      container.innerHTML = '';
  
      if (data.length === 0) {
        container.innerHTML = '<p>No tasks found.</p>';
        return;
      }
  
      data.forEach(task => {
        const div = document.createElement('div');
        div.className = 'task-item';
        div.innerHTML = `
          <strong>${task.title}</strong><br>
          ${task.description}<br>
          <em>${task.due_date}</em>
          <div class="task-actions">
            <button onclick="completeTask(${task.id})" class="complete">Complete</button>
            <button onclick="deleteTask(${task.id})">Delete</button>
          </div>
        `;
        container.appendChild(div);
      });
    });
  }
  
  function completeTask(id) {
    fetch('https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/complete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: "include",
        body: JSON.stringify({ task_id: id })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadTasks();
    //BAVISHAN ADD
        // Check for new achievements
        return fetch('https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/check_achievements.php', {
            credentials: "include"
        });
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.unlocked.length > 0) {
            data.unlocked.forEach(achievement => {
                alert(`Achievement Unlocked: ${achievement.name}\n${achievement.description}`);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
  
  function deleteTask(id) {
    fetch('https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/delete_task.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: "include",
      body: JSON.stringify({ task_id: id })
    })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      loadTasks();
    });
  }
  
  loadTasks();
  