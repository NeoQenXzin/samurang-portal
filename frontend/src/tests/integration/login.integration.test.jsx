import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";
import { configureStore } from "@reduxjs/toolkit";
import axios from "axios";
import Login from "../../Components/Login/Login.jsx";
import authReducer from "../../store/slices/authSlice.js";
import profileReducer from "../../store/slices/profileSlice.js";
import eventsReducer from "../../store/slices/eventsSlice.js";
import ordersReducer from "../../store/slices/ordersSlice.js";

// Configuration du store Redux pour les tests
const createTestStore = () =>
  configureStore({
    reducer: {
      auth: authReducer,
      profile: profileReducer,
      events: eventsReducer,
      orders: ordersReducer,
    },
  });

// Mock d'axios pour simuler les appels API
vi.mock("axios");

// Mock du hook useNavigate de react-router
const mockNavigate = vi.fn();
vi.mock("react-router-dom", async () => {
  const actual = await vi.importActual("react-router-dom");
  return {
    ...actual,
    useNavigate: () => mockNavigate,
  };
});

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000';

// Test de l'authentification
describe("Login Integration Test", () => {
  let store;

  // Avant chaque test
  beforeEach(() => {
    // Reset du store avant chaque test
    store = createTestStore();

    // Reset des mocks 
    vi.clearAllMocks();
    window.localStorage.clear();
  });

  // Helper pour rendre le composant avec tous ses providers
  const renderLoginComponent = () => {
    return render(
      <Provider store={store}>
        <BrowserRouter>
          <Login />
        </BrowserRouter>
      </Provider>
    );
  };

  // Test du cas de succès
  it("should authenticate user and redirect to dashboard with user data", async () => {
    const mockToken = "fake-jwt-token";
    // Simulation de la réponse API réussie
    axios.post.mockResolvedValueOnce({
      data: { token: mockToken },
    });

    renderLoginComponent();
    // fill the form
    // Simulation des interactions utilisateur
    const emailInput = screen.getByPlaceholderText(/email/i);
    const passwordInput = screen.getByPlaceholderText(/password/i);
    const submitButton = screen.getByRole("button", { name: /se connecter/i });

    fireEvent.change(emailInput, { target: { value: "test@example.com" } });
    fireEvent.change(passwordInput, { target: { value: "password123" } });
    fireEvent.click(submitButton);

    // Vérification des comportements attendus
    await waitFor(() => {
      expect(axios.post).toHaveBeenCalledWith(
        `${API_URL}/api/login_check`,
        {
          username: "test@example.com",
          password: "password123",
        }
      );
    });

    expect(mockNavigate).toHaveBeenCalledWith("/dashboard");
    expect(window.localStorage.getItem("token")).toBe(mockToken);
    expect(store.getState().auth.token).toBe(mockToken);
  });

  // Test du cas d'échec
  it("should show error message on failed login", async () => {
    // Simulation d'une erreur API
    axios.post.mockRejectedValueOnce(new Error("Invalid credentials"));

    renderLoginComponent();

    // Simulation des interactions utilisateur
    const emailInput = screen.getByPlaceholderText(/email/i);
    const passwordInput = screen.getByPlaceholderText(/password/i);
    const submitButton = screen.getByRole("button", { name: /se connecter/i });

    fireEvent.change(emailInput, { target: { value: "test@example.com" } });
    fireEvent.change(passwordInput, { target: { value: "wrongpassword" } });
    fireEvent.click(submitButton);

    // Vérification de l'affichage du message d'erreur
    await waitFor(() => {
      expect(
        screen.getByText("Email ou mot de passe incorrect")
      ).toBeInTheDocument();
    });
    expect(mockNavigate).not.toHaveBeenCalled();
  });
});
