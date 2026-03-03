import './bootstrap';

// Initialize theme immediately (before DOM is ready)
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'light') {
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
} else {
    // Default to dark mode
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
}

// Don't define Alpine stores here - Livewire will handle them
// Stores are defined in the layout after Livewire loads
