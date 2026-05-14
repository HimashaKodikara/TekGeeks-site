// resources/scripts/pages/code-highlight.js

// 1. Import the core library
import hljs from 'highlight.js/lib/core';

// 2. Import the CSS theme
// This line bundles the 'night-owl.css' file into your app.
import 'highlight.js/styles/night-owl.css';

// 3. Import only the languages you need (e.g., XML, JavaScript, PHP)
import xml from 'highlight.js/lib/languages/xml'; // For HTML
import javascript from 'highlight.js/lib/languages/javascript';
import php from 'highlight.js/lib/languages/php';
import css from 'highlight.js/lib/languages/css';

// 4. Register the languages
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('php', php);
hljs.registerLanguage('css', css);

// 5. Run highlighting
// This finds all <pre><code>...</code></pre> blocks and applies highlighting
document.addEventListener('DOMContentLoaded', (event) => {
    hljs.highlightAll();
});

// You can export hljs if other scripts need to access it
export default hljs;