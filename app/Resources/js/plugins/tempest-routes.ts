import { execSync } from 'child_process';
import type { Plugin } from 'vite';

export function tempestRoutes(): Plugin {
    const generate = () => {
        try {
            execSync('./tempest internal:export-routes', { stdio: 'inherit' });
        } catch {
            console.error('[tempest-routes] Failed to generate routes');
        }
    };

    return {
        name: 'tempest-routes',
        buildStart() {
            if (process.env.NODE_ENV === 'production') {
                generate();
            }
        },
        configureServer(server) {
            generate();
            server.watcher.on('change', (path) => {
                if (path.includes('Controllers')) {
                    generate();
                }
            });
        },
    };
}