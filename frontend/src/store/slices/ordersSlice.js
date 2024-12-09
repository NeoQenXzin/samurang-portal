import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

export const fetchOrders = createAsyncThunk('orders/fetchOrders', async (_, { rejectWithValue }) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            return rejectWithValue('Token non trouvé');
        }

        const response = await axios.get(`${API_URL}/api/next_orders`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        return response.data.member && response.data.member.length > 0 ? response.data.member : [];
    } catch (error) {
        return rejectWithValue('Erreur lors du chargement des commandes');
    }
});

const ordersSlice = createSlice({
    name: 'orders',
    initialState: { orders: [], loading: false, error: null },
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchOrders.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchOrders.fulfilled, (state, action) => {
                state.loading = false;
                state.orders = action.payload;
                state.error = null;
            })
            .addCase(fetchOrders.rejected, (state, action) => {
                state.loading = false;
                state.error = action.payload;
            });
    },
});

export default ordersSlice.reducer;