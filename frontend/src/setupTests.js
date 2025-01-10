import { expect, afterEach } from 'vitest';
import { cleanup } from '@testing-library/react';
import * as matchers from '@testing-library/jest-dom/matchers';
import { vi } from 'vitest';

// Extension des matchers expect
expect.extend(matchers);

// Nettoyage après chaque test
afterEach(() => {
    cleanup();
});

// Mock amélioré de localStorage
const localStorageMock = (() => {
    let store = {};
    return {
        getItem: vi.fn((key) => store[key] || null),
        setItem: vi.fn((key, value) => {
            store[key] = value.toString();
        }),
        clear: vi.fn(() => {
            store = {};
        }),
        removeItem: vi.fn((key) => {
            delete store[key];
        }),
    };
})();

// Configuration de l'environnement global
global.expect = expect;
global.vi = vi;

Object.defineProperty(window, 'localStorage', { value: localStorageMock }); 

process.env.REACT_APP_API_URL = 'http://localhost:8000';