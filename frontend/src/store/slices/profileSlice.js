import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

export const fetchUserProfile = createAsyncThunk(
  'profile/fetchUserProfile',
  async (_, { rejectWithValue }) => {
    try {
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('Token non trouvé');
      }

      const userResponse = await axios.get(`${API_URL}/api/current_user`);

      // provisoire Only fetch students if user is an instructor
      let students = [];
      if (userResponse.data.roles.includes('ROLE_INSTRUCTOR')) {
        try {
          const studentsResponse = await axios.get(`${API_URL}/api/students`);
          students = studentsResponse.data.member || [];
        } catch (error) {
          console.warn('Erreur lors du chargement des étudiants:', error);
          //provisoire  Continue without students data if there's an error
        }
      }

      return {
        user: {
          ...userResponse.data,
          dojang: userResponse.data.dojang || null,
          grade: userResponse.data.grade || null,
        },
        students,
      };
    } catch (error) {
      if (error.response?.status === 500) {
        return rejectWithValue('Erreur serveur. Veuillez réessayer plus tard.');
      }
      return rejectWithValue(error.message || 'Erreur lors du chargement des données');
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
  reducers: {
    clearProfile: (state) => {
      state.userData = null;
      state.error = null;
      state.isLoading = false;
    },
  },
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
        // If we get a 401 or 403, we should redirect to login
        if (action.payload === 'Token non trouvé') {
          localStorage.removeItem('token');
        }
      });
  },
});

export const { clearProfile } = profileSlice.actions;
export default profileSlice.reducer;
