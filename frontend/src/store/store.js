import { configureStore } from '@reduxjs/toolkit';
import profileReducer from './slices/profileSlice';
// import authReducer from './slices/authSlice';
// import userReducer from './slices/userSlice';
import eventsReducer from './slices/eventsSlice';
import ordersReducer from './slices/ordersSlice';

export const store = configureStore({
  reducer: {
    profile: profileReducer,
    // auth: authReducer,
    // user: userReducer,
    events: eventsReducer,
    orders: ordersReducer,
  },
});

export default store;
