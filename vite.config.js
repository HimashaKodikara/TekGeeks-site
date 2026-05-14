import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
                // Themes
                "resources/sass/themes/aurora.scss",
                "resources/sass/themes/earth.scss",
                "resources/sass/themes/flare.scss",
                "resources/sass/themes/lunar.scss",
                "resources/sass/themes/nebula.scss",
                "resources/sass/themes/night.scss",
                "resources/sass/themes/olive.scss",
                "resources/sass/themes/solar.scss",
                "resources/sass/themes/storm.scss",
                "resources/sass/smartapp.scss",

                // Fonts & Icons
                "resources/webfonts/fontawesome/fontawesome.scss",
                "resources/webfonts/smartadmin/sa-icons.scss",
                "node_modules/node-waves/dist/waves.min.css",
                "node_modules/jsvectormap/dist/jsvectormap.min.css",

                // Core Scripts
                "resources/scripts/core/smartNavigation.js",
                "resources/scripts/core/smartFilter.js",
                "resources/scripts/core/smartSlimscroll.js",
                "resources/scripts/core/saveloadscript.js",
                "resources/scripts/core/smartApp.js",

                // Page Specific Scripts (Full List)
                "resources/scripts/pages/blank.js",
                "resources/scripts/pages/apexarea chart.js",
                "resources/scripts/pages/apexbarchart.js",
                "resources/scripts/pages/apexboxwhiskerchart.js",
                "resources/scripts/pages/apexbubblechart.js",
                "resources/scripts/pages/apexcandlestickchart.js",
                "resources/scripts/pages/apexcolumnchart.js",
                "resources/scripts/pages/apexfunnelchart.js",
                "resources/scripts/pages/apexheatmapchart.js",
                "resources/scripts/pages/apexlinechart.js",
                "resources/scripts/pages/apexmixedcombochart.js",
                "resources/scripts/pages/apexpiedonut.js",
                "resources/scripts/pages/apexpolarareachart.js",
                "resources/scripts/pages/apexradarchart.js",
                "resources/scripts/pages/apexradialbarscirclechart.js",
                "resources/scripts/pages/apexrangeareachart.js",
                "resources/scripts/pages/apexscatterchart.js",
                "resources/scripts/pages/apexsparkline.js",
                "resources/scripts/pages/apextimelinechart.js",
                "resources/scripts/pages/apextreemapchart.js",
                "resources/scripts/pages/auth-animation.js",
                "resources/scripts/pages/controlcenterdashboard.js",
                "resources/scripts/pages/easypiechart.js",
                "resources/scripts/pages/marketingdashboard.js",
                "resources/scripts/pages/projectmanagementdashboard.js",
                "resources/scripts/pages/subscriptiondashboard.js",
                "resources/scripts/pages/code-highlight.js",
                "resources/scripts/pages/peitycharts.js",
                "resources/scripts/pages/systemmail.js",
                "resources/scripts/pages/checkboxandradio.js",
                "resources/scripts/pages/search.js",
                "resources/scripts/pages/smarttablesfuzzymatching.js",
                "resources/scripts/pages/smarttablesimportexportdata.js",
                "resources/scripts/pages/smarttablesjsonsource.js",
                "resources/scripts/pages/smarttablesmanagerecords.js",
                "resources/scripts/pages/smarttableminimal.js",
                "resources/scripts/pages/smarttableresponsive.js",
                "resources/scripts/pages/smarttablesserverside.js",
                "resources/scripts/pages/profile.js",
                "resources/scripts/pages/listfilter.js",
                "resources/scripts/pages/messenger.js",
                "resources/scripts/optional/smartnotes.js",
                "resources/scripts/pages/landing.js",
                "resources/scripts/pages/refresh-panel.js",
                "resources/scripts/pages/position.js",
                "resources/scripts/pages/visibilitygenerator.js",
                "resources/scripts/pages/systemicons.js",
                "resources/scripts/pages/fontawesome.js",
                "resources/scripts/pages/smartadminicons.js",
                "resources/scripts/pages/stackgenerator.js",
                "resources/scripts/pages/stacklibrary.js",
                "resources/scripts/pages/tablesstylegenerator.js",
                "resources/scripts/pages/apexslopechart.js",
                "resources/scripts/pages/streamline.js",
                "resources/scripts/pages/fullcalendar.js",
                "resources/scripts/pages/usercontact.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            // Helps Vite find the fonts and images in your resource folder
            "@": path.resolve(__dirname, "resources"),
            "~": path.resolve(__dirname, "node_modules"),
        },
    },
    build: {
        // Increases the warning limit for your many JS files
        chunkSizeWarningLimit: 2000,
        // Ensures the output goes exactly where Laravel expects it
        outDir: "public/build",
        emptyOutDir: true,
    },
});
