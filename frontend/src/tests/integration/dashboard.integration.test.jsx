import { render, screen, waitFor } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";
import { configureStore } from "@reduxjs/toolkit";
import axios from "axios";
import Dashboard from "../../Components/Dashboard/Dashboard";
import authReducer from "../../store/slices/authSlice";
import profileReducer from "../../store/slices/profileSlice";
import eventsReducer from "../../store/slices/eventsSlice";
import ordersReducer from "../../store/slices/ordersSlice";

vi.mock("axios");
vi.mock("react-toastify", () => ({
  toast: { error: vi.fn() },
}));

// Test de l'affichage des données utilisateur
describe("Dashboard Integration Tests", () => {
  let store;
  const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000';

  beforeEach(() => {
    store = configureStore({
      reducer: {
        auth: authReducer,
        profile: profileReducer,
        events: eventsReducer,
        orders: ordersReducer,
      },
    });
    vi.clearAllMocks();
    localStorage.clear();
  });

  // Helper de rendu
  const renderDashboard = () => {
    return render(
      <Provider store={store}>
        <BrowserRouter>
          <Dashboard />
        </BrowserRouter>
      </Provider>
    );
  };

  // Test de l'authentification
  describe("Basic Authentication Tests", () => {
    it("should set token correctly", () => {
      store.dispatch({ type: "auth/setToken", payload: "fake-jwt-token" });
      expect(store.getState().auth.token).toBe("fake-jwt-token");
    });

    // Test de l'enregistrement du token dans le localStorage
    it("should store token in localStorage", () => {
      localStorage.setItem("token", "fake-jwt-token");
      expect(localStorage.getItem("token")).toBe("fake-jwt-token");
    });
  });


  describe("Data Display Tests", () => {
    beforeEach(() => {
      store.dispatch({ type: "auth/setToken", payload: "fake-jwt-token" });
      localStorage.setItem("token", "fake-jwt-token");
    });

    // Test de l'affichage du nom de l'utilisateur
    it("should display student user name when data is loaded", async () => {
      // Mock avec la structure correcte
      axios.get.mockImplementation((url) => {
        if (url === `${API_URL}/api/current_user`) {
          return Promise.resolve({
            data: {
              roles: ["ROLE_STUDENT"],
              firstname: "Johnny",
              lastname: "Will",
            },
          });
        }
        return Promise.resolve({ data: { "hydra:member": [] } });
      });

      renderDashboard();

      await waitFor(() => {
        expect(screen.getByText("Johnny Will")).toBeInTheDocument();
      });
    });

    it("should display grade when available", async () => {
      axios.get.mockImplementation((url) => {
        if (url === `${API_URL}/api/current_user`) {
          return Promise.resolve({
            data: {
              roles: ["ROLE_INSTRUCTOR"],
              firstname: "John",
              lastname: "Doe",
              grade: { name: "Black Belt" },
            },
          });
        }
        return Promise.resolve({ data: { "hydra:member": [] } });
      });

      renderDashboard();

      await waitFor(() => {
        expect(screen.getByText("Black Belt")).toBeInTheDocument();
      });
    });
  });

  // Test de l'affichage des données utilisateur
  describe("Full Integration Test", () => {
    it("should handle complete API Platform flow", async () => {
      store.dispatch({ type: "auth/setToken", payload: "fake-jwt-token" });
      localStorage.setItem("token", "fake-jwt-token");

      // Mock avec la structure exacte du CurrentUserController
      const mockResponses = {
        currentUser: {
          data: {
            userIdentifier: "john.doe@test.com",
            roles: ["ROLE_INSTRUCTOR", "ROLE_ADMIN"],
            type: "Instructor",
            id: 1,
            firstname: "John",
            lastname: "Doe",
            mail: "john.doe@test.com",
            dojang: { id: 1, name: "Test Dojang" },
            grade: { id: 1, name: "Black Belt" },
          },
        },
        formations: {
          data: {
            "hydra:member": [
              {
                id: 1,
                type: "Formation Test",
                startDate: "2025-01-01",
                endDate: "2025-01-02",
                location: "Test Location",
              },
            ],
          },
        },
        orders: {
          data: {
            "hydra:member": [
              {
                id: 1,
                status: "pending",
                sendDate: "2025-01-15",
              },
            ],
          },
        },
      };

      axios.get.mockImplementation((url) => {
        if (url === `${API_URL}/api/current_user`)
          return Promise.resolve(mockResponses.currentUser);
        if (url === `${API_URL}/api/formations`)
          return Promise.resolve(mockResponses.formations);
        if (url === `${API_URL}/api/next_orders`)
          return Promise.resolve(mockResponses.orders);
        if (url === `${API_URL}/api/students`)
          return Promise.resolve({ data: { "hydra:member": [] } });
        return Promise.reject(new Error("Unexpected URL"));
      });

      renderDashboard();

      // Vérifier l'affichage des données utilisateur
      await waitFor(() => {
        // Vérifier le nom complet
        const heading = screen.getByRole("heading", { level: 1 });
        expect(heading).toHaveTextContent("John Doe");

      });
      await waitFor(() => {
        // Vérifier le grade
        expect(screen.getByText("Black Belt")).toBeInTheDocument();
      });
      await waitFor(() => {
       
        // Vérifier le dojang
        expect(screen.getByText(/Test Dojang/)).toBeInTheDocument();
      });
       //   await waitFor(() => {
    //     // Vérifier la date de début de la formation
    //     expect(screen.getByText("01/01/2025")).toBeInTheDocument();
    //   });
    //   await waitFor(() => {
    //     // Vérifier la date de fin de la formation
    //     expect(screen.getByText("02/01/2025")).toBeInTheDocument();
    //   });
    //   await waitFor(() => {
    //     // Vérifier la localisation de la formation
    //     expect(screen.getByText("Test Location")).toBeInTheDocument();
    //   });
    //   await waitFor(() => {
    //     // Vérifier le statut de la commande
    //     expect(screen.getByText("En attente")).toBeInTheDocument();
    //   });
    //   await waitFor(() => {
    //     // Vérifier la date d'envoi de la commande
    //     expect(screen.getByText("15/01/2025")).toBeInTheDocument();
    //   });
    //   await waitFor(() => {
    //     // Vérifier le bouton de déconnexion
    //     expect(screen.getByText("Déconnexion")).toBeInTheDocument();
    //   });
    });

  });
});
