//BAVISHAN ADD
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('achievementsModal');
    const achievementsBtn = document.getElementById('showAchievements');
    const closeBtn = document.querySelector('.close');
    
    // Open modal when button clicked
    achievementsBtn.addEventListener('click', function() {
        fetchAchievements();
        modal.style.display = 'block';
    });
    
    // Close modal when X clicked
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    function fetchAchievements() {
        fetch('server/get_achievements.php', {
            credentials: "include"
        })
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('achievementsList');
            list.innerHTML = '';
            
            if (data.length === 0) {
                list.innerHTML = '<p>No achievements yet. Keep completing tasks to earn achievements!</p>';
            } else {
                data.forEach(achievement => {
                    const div = document.createElement('div');
                    div.className = 'achievement-item';
                    div.innerHTML = `
                        <h3>${achievement.name} ${achievement.completed ? '✓' : ''}</h3>
                        <p>${achievement.description}</p>
                        <progress value="${achievement.progress}" max="${achievement.condition_value}"></progress>
                    `;
                    list.appendChild(div);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching achievements:', error);
        });
    }
});