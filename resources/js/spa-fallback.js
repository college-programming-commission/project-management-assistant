// Fallback SPA navigation if Filament doesn't render wire:navigate
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Livewire === 'undefined' || typeof Livewire.navigate === 'undefined') {
        console.warn('Livewire navigate not available');
        return;
    }

    // Add click handler to all internal links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        
        if (!link) return;
        
        const href = link.getAttribute('href');
        
        // Skip if:
        // - No href
        // - External link
        // - Already has wire:navigate
        // - Download link
        // - Hash link
        // - Has target="_blank"
        if (!href || 
            href.startsWith('http') && !href.startsWith(window.location.origin) ||
            link.hasAttribute('wire:navigate') ||
            link.hasAttribute('download') ||
            href.startsWith('#') ||
            link.target === '_blank') {
            return;
        }
        
        // Navigate using Livewire
        e.preventDefault();
        Livewire.navigate(href);
    }, true);
    
    console.log('✓ SPA fallback navigation enabled');
});
