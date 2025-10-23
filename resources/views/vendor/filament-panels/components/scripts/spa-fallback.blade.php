<script>
// Inline SPA fallback for Filament admin
(function() {
    console.log('📦 Inline SPA fallback loading...');
    
    let spaActive = false;
    
    function setupSPA() {
        if (spaActive) return;
        
        if (typeof window.Livewire === 'undefined') {
            console.log('⏳ Waiting for Livewire...');
            return;
        }
        
        // Wait for Livewire to fully initialize with navigate
        if (typeof window.Livewire.navigate !== 'function') {
            console.log('⏳ Waiting for Livewire.navigate...');
            return;
        }
        
        console.log('✅ Livewire ready, setting up SPA navigation...');
        spaActive = true;
        
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (!href || href === '#') return;
            
            // Only handle internal links
            try {
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;
            } catch {
                return;
            }
            
            // Skip special links
            if (link.hasAttribute('wire:navigate') ||
                link.hasAttribute('download') ||
                link.target === '_blank') {
                return;
            }
            
            console.log('🚀 SPA navigate:', href);
            e.preventDefault();
            e.stopPropagation();
            window.Livewire.navigate(href);
        }, true);
        
        console.log('✅ SPA fallback active');
    }
    
    // Listen for Livewire init event
    document.addEventListener('livewire:init', function() {
        console.log('🎉 Livewire initialized');
        setupSPA();
    });
    
    // Also try after DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSPA);
    } else {
        setupSPA();
    }
    
    // Retry periodically for 3 seconds if not ready
    let attempts = 0;
    const maxAttempts = 30;
    const retry = setInterval(function() {
        attempts++;
        if (spaActive || attempts >= maxAttempts) {
            clearInterval(retry);
            if (!spaActive) {
                console.error('❌ Failed to initialize SPA after 3 seconds');
            }
            return;
        }
        setupSPA();
    }, 100);
})();
</script>
