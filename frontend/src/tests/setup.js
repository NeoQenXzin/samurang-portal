import '@testing-library/jest-dom'
import { vi } from 'vitest';

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(key => null),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn(),
};

Object.defineProperty(window, 'localStorage', {
  value: localStorageMock,
  writable: true
});

// Mock process.env
vi.stubGlobal('process', {
  env: {
    REACT_APP_API_URL: process.env.API_URL ||'http://localhost:8000'
  }
});