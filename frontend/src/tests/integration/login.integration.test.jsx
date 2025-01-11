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

// Utilitaire pour configurer un store Redux de test
const setupTestStore = () =>
  configureStore({
    reducer: {
      auth: authReducer,
      profile: profileReducer,
      events: eventsReducer,
      orders: ordersReducer,
    },
  });

// Mock d'axios pour les appels API
vi.mock("axios");

// Mock de useNavigate pour simuler la navigation
const mockNavigate = vi.fn();
vi.mock("react-router-dom", async () => {
  const actual = await vi.importActual("react-router-dom");
  return {
    ...actual,
    useNavigate: () => mockNavigate,
  };
});

// URL de l'API (utilise une variable d'environnement avec une valeur par défaut)
const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

describe("Login Component Integration Tests", () => {
  let store;

  beforeEach(() => {
    // Réinitialisation du store et des mocks avant chaque test
    store = setupTestStore();
    vi.clearAllMocks();
    window.localStorage.clear();
  });

  /**
   * Helper pour rendre le composant Login avec les providers nécessaires
   */
  const renderLoginComponent = () => {
    render(
      <Provider store={store}>
        <BrowserRouter>
          <Login />
        </BrowserRouter>
      </Provider>
    );
  };

  it("should authenticate the user, save token, and redirect to the dashboard", async () => {
    const mockToken = "fake-jwt-token";

    // Mock de la réponse réussie de l'API
    axios.post.mockResolvedValueOnce({ data: { token: mockToken } });

    renderLoginComponent();

    // Simuler le remplissage du formulaire et l'envoi
    const emailInput = screen.getByPlaceholderText(/email/i);
    const passwordInput = screen.getByPlaceholderText(/password/i);
    const submitButton = screen.getByRole("button", { name: /se connecter/i });

    fireEvent.change(emailInput, { target: { value: "test@example.com" } });
    fireEvent.change(passwordInput, { target: { value: "password123" } });
    fireEvent.click(submitButton);

    // Vérification des appels API
    await waitFor(() => {
      expect(axios.post).toHaveBeenCalledWith(`${API_URL}/api/login_check`, {
        username: "test@example.com",
        password: "password123",
      });
    });

    // Vérification de la navigation et des données stockées
    expect(mockNavigate).toHaveBeenCalledWith("/dashboard");
    expect(window.localStorage.getItem("token")).toBe(mockToken);
    expect(store.getState().auth.token).toBe(mockToken);
  });

  it("should display an error message when login fails", async () => {
    // Mock d'une erreur lors de l'appel à l'API
    axios.post.mockRejectedValueOnce(new Error("Invalid credentials"));

    renderLoginComponent();

    // Simuler le remplissage du formulaire avec des données invalides
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

    // Vérification que la navigation ne s'est pas produite
    expect(mockNavigate).not.toHaveBeenCalled();
  });
});
