import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

export const fetchUserProfile = createAsyncThunk(
  'profile/fetchUserProfile',
  async (_, { rejectWithValue }) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) {
        return rejectWithValue('Veuillez vous connecter pour accéder à votre profil');
      }

      const headers = { Authorization: `Bearer ${token}` };
      const endpoints = {
        user: `${API_URL}/api/current_user`,
        events: `${API_URL}/api/formations`,
        orders: `${API_URL}/api/next_orders`,
      };

      const [userResponse, eventsResponse, orderResponse] = await Promise.all([
        axios.get(endpoints.user, { headers }),
        axios.get(endpoints.events, { headers }),
        axios.get(endpoints.orders, { headers }),
      ]);

      return {
        user: userResponse.data,
        events: eventsResponse.data["hydra:member"],
        nextOrder: orderResponse.data,
      };
    } catch (error) {
      return rejectWithValue('Erreur lors du chargement de vos données');
    }
  }
);

const profileSlice = createSlice({
  name: 'profile',
  initialState: {
    userData: null,
    isLoading: false,
    error: null,
  },
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchUserProfile.pending, (state) => {
        state.isLoading = true;
        state.error = null;
      })
      .addCase(fetchUserProfile.fulfilled, (state, action) => {
        state.isLoading = false;
        state.userData = action.payload;
        state.error = null;
      })
      .addCase(fetchUserProfile.rejected, (state, action) => {
        state.isLoading = false;
        state.error = action.payload;
      });
  },
});

export default profileSlice.reducer;