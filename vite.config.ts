import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vite'
import tempest from 'vite-plugin-tempest'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import { resolve } from 'path'
import { tempestRoutes } from './app/Resources/js/plugins/tempest-routes';


export default defineConfig({
	plugins: [
		svelte(),
		tailwindcss(),
		tempest(),
		tempestRoutes()
	],
	resolve: {
		alias: {
			'@components': resolve(__dirname, '/app/Resources/pages/Components'),
			'@layouts': resolve(__dirname, '/app/Resources/pages/Layouts'),
			'@pages': resolve(__dirname, '/app/Resources/pages'),
			'@utils': resolve(__dirname, '/app/Resources/js/utils.ts'),
			'@route': resolve(__dirname, '/app/Resources/js/routes/route.ts'),
			'@t': resolve(__dirname, '/app/Resources/js/Types'),
			'@tempest': resolve(__dirname, '.tempest'),
		}
	}
})
