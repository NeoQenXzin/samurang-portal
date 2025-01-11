import { defineConfig } from 'vitest/config'
import react from '@vitejs/plugin-react';
export default defineConfig({
    plugins: [react()],
    test: {
        globals: true,
        environment: 'jsdom',
        setupFiles: ['./src/setupTests.js'],
        include: ['**/*.{test,spec}.{js,jsx}'],
        deps: {
            inline: ['@testing-library/jest-dom'],
        },
        coverage: {
            provider: 'v8',
            reporter: ['text', 'json', 'html'],
            reportsDirectory: './coverage'
        }
    }
}) 