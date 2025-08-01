{{-- Loading Styles for Filament Admin Panel --}}
<style>
/* Enhanced shimmer effect for Filament */
.shimmer {
    background: linear-gradient(
        90deg,
        #f1f5f9 0%,
        #e2e8f0 20%,
        #e2e8f0 40%,
        #f1f5f9 100%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Loading overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-left: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Page transition effects */
.page-enter-active,
.page-leave-active {
    transition: opacity 0.3s ease;
}

.page-enter-from,
.page-leave-to {
    opacity: 0;
}

/* Card loading animations */
.card-loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Burger Menu Integration Styles */
@media (max-width: 1024px) {
    /* Hide default Filament sidebar on mobile */
    .fi-sidebar {
        display: none !important;
    }
    
    /* Adjust main content area for mobile */
    .fi-main {
        margin-left: 0 !important;
    }
    
    /* Adjust topbar for burger menu */
    .fi-topbar {
        padding-left: 1rem !important;
    }
}

/* Burger menu z-index management */
.fi-topbar {
    position: relative;
    z-index: 30;
}

/* Mobile navigation improvements */
@media (max-width: 768px) {
    /* Ensure burger menu is always visible */
    .mobile-burger-menu {
        position: relative;
        z-index: 60;
    }
    
    /* Adjust Filament components for mobile */
    .fi-header-heading {
        font-size: 1.25rem !important;
    }
    
    .fi-breadcrumbs {
        display: none !important;
    }
    
    /* Mobile-friendly widgets */
    .fi-wi-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .shimmer {
        background: linear-gradient(
            90deg,
            #1e293b 0%,
            #334155 20%,
            #334155 40%,
            #1e293b 100%
        );
        background-size: 200% 100%;
    }
    
    .loading-overlay {
        background: rgba(15, 23, 42, 0.8);
    }
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .shimmer {
        animation-duration: 2s;
    }
    
    /* Smooth mobile interactions */
    * {
        -webkit-tap-highlight-color: transparent;
    }
    
    /* Better touch targets */
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Burger menu specific styles */
.burger-menu-overlay {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Animation for burger menu */
@keyframes slideInFromLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.slide-in-left {
    animation: slideInFromLeft 0.3s ease-out;
}

/* Responsive topbar adjustments */
@media (max-width: 1024px) {
    .fi-topbar-start {
        flex: 1;
        justify-content: flex-start;
    }
    
    .fi-topbar-end {
        flex: none;
    }
}
</style>