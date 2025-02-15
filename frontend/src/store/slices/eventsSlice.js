import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

// Récupérer les événements
export const fetchEvents = createAsyncThunk('events/fetchEvents', async (_, { rejectWithValue }) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            return rejectWithValue('Token non trouvé');
        }

        const response = await axios.get(`${API_URL}/api/formations`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        // Modifier ici pour gérer  réponse API
        return response.data.member || [];
    } catch (error) {
        return rejectWithValue('Erreur lors du chargement des événements');
    }
});

export const toggleEventParticipation = createAsyncThunk(
  'events/toggleParticipation',
  async ({ formationId, isParticipating }, { rejectWithValue }) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) {
        return rejectWithValue('Token non trouvé');
      }

      // Inscription ou désinscription à l'événement
      const endpoint = isParticipating ? 'unregister' : 'register';
      await axios.post(
        `${API_URL}/api/formations/${formationId}/${endpoint}`,
        {},
        {
          headers: { Authorization: `Bearer ${token}` }
        }
      );

      // Recharger les événements après l'inscription/désinscription
      const response = await axios.get(`${API_URL}/api/formations`, {
        headers: { Authorization: `Bearer ${token}` }
      });

      // Retourner les événements modifiés
      return response.data.member || [];
    } catch (error) {
      return rejectWithValue('Erreur lors de la modification de l\'inscription');
    }
  }
);

const eventsSlice = createSlice({
    name: 'events',
    initialState: {
        events: [],
        loading: false,
        error: null
    },
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchEvents.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchEvents.fulfilled, (state, action) => {
                state.loading = false;
                state.events = action.payload;
                state.error = null;
            })
            .addCase(fetchEvents.rejected, (state, action) => {
                state.loading = false;
                state.error = action.payload;
            })
            .addCase(toggleEventParticipation.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(toggleEventParticipation.fulfilled, (state, action) => {
                state.loading = false;
                state.events = action.payload;
                state.error = null;
            })
            .addCase(toggleEventParticipation.rejected, (state, action) => {
                state.loading = false;
                state.error = action.payload;
            });
    }
});

export default eventsSlice.reducer;