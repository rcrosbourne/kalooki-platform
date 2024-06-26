import './bootstrap';
import '../css/app.css';

import React from 'react';
import {createRoot} from 'react-dom/client';
import {createInertiaApp} from '@inertiajs/inertia-react';
import {InertiaProgress} from '@inertiajs/progress';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {MantineProvider} from "@mantine/core";
import {NotificationsProvider} from "@mantine/notifications";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
    setup({el, App, props}) {
        const root = createRoot(el);
        root.render(
            <MantineProvider withGlobalStyles withNormalizeCSS>
                <NotificationsProvider>
                    <App {...props} />
                </NotificationsProvider>
            </MantineProvider>
        );
    },
});
InertiaProgress.init(
    {
        color: '#4B5563'
    }
);
