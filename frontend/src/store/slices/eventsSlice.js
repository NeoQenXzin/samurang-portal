import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

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

        // Modification ici pour gérer la réponse de l'API
        return response.data.member || [];
    } catch (error) {
        return rejectWithValue('Erreur lors du chargement des événements');
    }
});

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
            });
    }
});

export default eventsSlice.reducer;