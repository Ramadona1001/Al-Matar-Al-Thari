/**
 * Language Switching System
 * Handles loading and switching between English and Arabic
 */

let currentLang = localStorage.getItem('language') || 'en';
let translations = {};

/**
 * Load language JSON file
 */
async function loadLanguage(lang) {
  try {
    const response = await fetch(`/assets/lang/${lang}.json`);
    if (!response.ok) {
      throw new Error(`Failed to load language file: ${lang}.json`);
    }
    translations = await response.json();
    return translations;
  } catch (error) {
    console.error('Error loading language:', error);
    // Fallback to English if Arabic fails
    if (lang === 'ar') {
      return loadLanguage('en');
    }
  }
}

/**
 * Change language by redirecting to appropriate page
 */
function changeLanguage(lang) {
  currentLang = lang;
  localStorage.setItem('language', lang);
  
  // Get current page name
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  
  // Determine target page
  let targetPage;
  if (lang === 'ar') {
    // Switch to Arabic version
    if (currentPage === 'index.html' || currentPage === '') {
      targetPage = 'index-ar.html';
    } else if (currentPage.endsWith('-ar.html')) {
      // Already on Arabic page
      return;
    } else {
      // Replace .html with -ar.html
      targetPage = currentPage.replace('.html', '-ar.html');
    }
  } else {
    // Switch to English version
    if (currentPage === 'index-ar.html') {
      targetPage = 'index.html';
    } else if (currentPage.endsWith('-ar.html')) {
      // Replace -ar.html with .html
      targetPage = currentPage.replace('-ar.html', '.html');
    } else {
      // Already on English page
      return;
    }
  }
  
  // Redirect to target page
  window.location.href = targetPage;
}

/**
 * Update language switcher button states
 */
function updateLanguageSwitcher() {
  const buttons = document.querySelectorAll('.lang-switcher button');
  buttons.forEach(btn => {
    if (btn.dataset.lang === currentLang) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });
}

/**
 * Get translation by key path (e.g., 'nav.home')
 */
function t(key) {
  const keys = key.split('.');
  let value = translations;
  
  for (const k of keys) {
    if (value && typeof value === 'object' && k in value) {
      value = value[k];
    } else {
      console.warn(`Translation key not found: ${key}`);
      return key;
    }
  }
  
  return value || key;
}

/**
 * Update all page content with translations
 * Note: data-i18n functionality has been removed
 */
function updatePageContent() {
  // No longer updating content - data-i18n removed
  // This function is kept for compatibility but does nothing
}

/**
 * Initialize language system
 */
async function initLanguage() {
  // Set initial direction
  document.documentElement.setAttribute('dir', currentLang === 'ar' ? 'rtl' : 'ltr');
  document.documentElement.setAttribute('lang', currentLang);
  
  // Update language switcher
  updateLanguageSwitcher();
  
  // Add event listeners to language switcher buttons
  document.querySelectorAll('.lang-switcher button').forEach(btn => {
    // Skip if button already has onclick handler
    if (!btn.onclick) {
      btn.addEventListener('click', (e) => {
        const lang = btn.dataset.lang;
        if (lang) {
          changeLanguage(lang);
        }
      });
    }
  });
}

// Initialize on DOM load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initLanguage);
} else {
  initLanguage();
}

// Export for use in other scripts
window.changeLanguage = changeLanguage;
window.t = t;
window.currentLang = currentLang;

