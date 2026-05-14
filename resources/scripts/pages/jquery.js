import $ from 'jquery';
window.$ = window.jQuery = $;

// ✅ Import Parsley directly from node_modules for Vite compatibility
import 'parsley';
import 'parsley-en';
import 'parsley-en-extra';


// ✅ Optional: set locale globally
if (window.Parsley) {
    window.Parsley.setLocale('en');
}
