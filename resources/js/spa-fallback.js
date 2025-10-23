// Fallback SPA navigation if Filament doesn't render wire:navigate
console.log('📦 SPA fallback script loaded');

function initSpaFallback() {
    if (typeof window.Livewire === 'undefined') {
        console.warn('⚠️ Livewire not found, retrying in 100ms...');
        setTimeout(initSpaFallback, 100);
        return;
    }

    if (typeof window.Livewire.navigate !== 'function') {
        console.warn('⚠️ Livewire.navigate not available');
        return;
    }

    console.log('✓ Livewire found, initializing SPA fallback...');

    // Add click handler to all internal links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        
        if (!link) return;
        
        const href = link.getAttribute('href');
        
        // Skip if no href
        if (!href) return;
        
        // Skip external links (not same origin)
        try {
            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;
        } catch (err) {
            return;
        }
        
        // Skip if:
        // - Already has wire:navigate (Filament rendered it correctly)
        // - Download link
        // - Hash link only
        // - Has target="_blank"
        if (link.hasAttribute('wire:navigate') ||
            link.hasAttribute('download') ||
            href === '#' ||
            link.target === '_blank') {
            return;
        }
        
        // Navigate using Livewire SPA
        console.log('🚀 SPA Navigate to:', href);
        e.preventDefault();
        e.stopPropagation();
        window.Livewire.navigate(href);
    }, true); // Use capture phase
    
    console.log('✅ SPA fallback navigation enabled');
}

// Try to init when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSpaFallback);
} else {
    // DOM already loaded
    initSpaFallback();
}
