{{-- Loading Overlay Component --}}
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="text-center">
        <div class="loading-spinner mb-4"></div>
        <p class="text-gray-600 text-sm">Loading...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show loading overlay on page transitions
    const overlay = document.getElementById('loading-overlay');
    
    // Show loading on form submissions
    document.addEventListener('submit', function() {
        if (overlay) {
            overlay.style.display = 'flex';
        }
    });
    
    // Show loading on navigation
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href]');
        if (link && !link.hasAttribute('target') && !link.href.includes('#')) {
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }
    });
    
    // Hide loading overlay when page is loaded
    window.addEventListener('load', function() {
        if (overlay) {
            overlay.style.display = 'none';
        }
    });
    
    // Hide loading overlay on back/forward navigation
    window.addEventListener('pageshow', function() {
        if (overlay) {
            overlay.style.display = 'none';
        }
    });
});
</script>
