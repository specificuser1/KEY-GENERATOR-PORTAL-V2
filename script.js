document.addEventListener('DOMContentLoaded', function() {
    const getKeyBtn = document.getElementById('getKeyBtn');
    const modal = document.getElementById('keyModal');
    const generatedKey = document.getElementById('generatedKey');
    const nextKeyTimer = document.getElementById('nextKeyTimer');
    
    // Handle cooldown timer if exists
    const cooldownTimer = document.getElementById('cooldownTimer');
    if (cooldownTimer) {
        const remaining = parseInt(cooldownTimer.dataset.remaining);
        updateTimer(remaining);
    }
    
    // Get Key button click handler
    if (getKeyBtn && !getKeyBtn.disabled) {
        getKeyBtn.addEventListener('click', function() {
            fetch('redeem_key.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        generatedKey.textContent = data.key;
                        modal.classList.add('show');
                        
                        // Update next key timer
                        const nextAvailable = new Date();
                        nextAvailable.setHours(nextAvailable.getHours() + 6);
                        updateNextKeyTimer(nextAvailable);
                        
                        // Refresh page after 2 seconds to update stats
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    }
});

function closeModal() {
    document.getElementById('keyModal').classList.remove('show');
}

function copyKey() {
    const keyText = document.getElementById('generatedKey').textContent;
    navigator.clipboard.writeText(keyText).then(() => {
        alert('Key copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy key');
    });
}

function updateTimer(remainingSeconds) {
    const timerElement = document.getElementById('timer');
    if (!timerElement) return;
    
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours}h ${minutes}m ${secs}s`;
    }
    
    function tick() {
        if (remainingSeconds <= 0) {
            location.reload();
            return;
        }
        
        timerElement.textContent = formatTime(remainingSeconds);
        remainingSeconds--;
        setTimeout(tick, 1000);
    }
    
    tick();
}

function updateNextKeyTimer(nextAvailable) {
    const timerElement = document.getElementById('nextKeyTimer');
    if (!timerElement) return;
    
    function update() {
        const now = new Date();
        const diff = nextAvailable - now;
        
        if (diff <= 0) {
            timerElement.textContent = 'Available now!';
            return;
        }
        
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        
        timerElement.textContent = `${hours}h ${minutes}m ${seconds}s`;
        setTimeout(update, 1000);
    }
    
    update();
}
