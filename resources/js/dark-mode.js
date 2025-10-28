// Dark Mode Persistence Handler
window.addEventListener('load', function() {
    const savedMode = localStorage.getItem('mode');
    
    console.log('Dark Mode Script Loaded. Saved mode:', savedMode);
    
    // Apply saved dark mode
    if (savedMode === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.body.classList.add('dark-mode');
        console.log('Dark mode applied');
    }
    
    // Small delay to ensure DOM is ready and theme scripts loaded
    setTimeout(function() {
        // Update toggle buttons based on saved mode
        if (savedMode === 'dark') {
            document.querySelectorAll('.dark-mode-toggle').forEach(toggle => {
                if (toggle.getAttribute('data-layout') === 'dark') {
                    toggle.classList.add('active');
                } else {
                    toggle.classList.remove('active');
                }
            });
        }
        
        // Override and handle dark mode toggle clicks
        document.querySelectorAll('.dark-mode-toggle').forEach(toggle => {
            // Remove any existing listeners by cloning
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const layout = this.getAttribute('data-layout');
                
                console.log('Toggle clicked. Layout:', layout);
                
                // Remove active class from all toggles
                document.querySelectorAll('.dark-mode-toggle').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked toggle
                this.classList.add('active');
                
                // Toggle dark mode
                if (layout === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('mode', 'dark');
                    console.log('Dark mode enabled and saved');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('mode', 'light');
                    console.log('Light mode enabled and saved');
                }
            });
        });
    }, 100);
});
