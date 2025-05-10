import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
import Analytics from './components/analytics';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <>
                <Analytics 
                    url={import.meta.env.VITE_ANALYTICS_URL} 
                    websiteId={import.meta.env.VITE_ANALYTICS_WEBSITE_ID} 
                />
                <App {...props} />
            </>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
