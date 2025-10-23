<script>
// Inline SPA fallback for Filament admin
(function() {
    console.log('📦 Inline SPA fallback loading...');
    
    function setupSPA() {
        if (typeof window.Livewire === 'undefined') {
            setTimeout(setupSPA, 100);
            return;
        }
        
        if (typeof window.Livewire.navigate !== 'function') {
            console.warn('Livewire.navigate not available');
            return;
        }
        
        console.log('✅ Setting up SPA navigation...');
        
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
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSPA);
    } else {
        setupSPA();
    }
})();
</script>
