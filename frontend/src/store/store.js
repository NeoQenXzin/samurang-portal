// import { configureStore } from '@reduxjs/toolkit';
// import { persistStore, persistReducer } from 'redux-persist';
// import storage from 'redux-persist/lib/storage';
// import profileReducer from './slices/profileSlice';
// // import authReducer from './slices/authSlice';
// // import userReducer from './slices/userSlice';
// import eventsReducer from './slices/eventsSlice';
// import ordersReducer from './slices/ordersSlice';

// const persistConfig = {
//   key: 'root',
//   storage,
//   whitelist: ['profile', 'events', 'orders']
// };

// const persistedProfileReducer = persistReducer(persistConfig, profileReducer);

// export const store = configureStore({
//   reducer: {
//     profile: persistedProfileReducer,
//     // auth: authReducer,
//     // user: userReducer,
//     events: eventsReducer,
//     orders: ordersReducer,
//   },
//   middleware: (getDefaultMiddleware) =>
//     getDefaultMiddleware({
//       serializableCheck: false,
//     }),
// });

// export const persistor = persistStore(store);

// export default store;

import { configureStore } from '@reduxjs/toolkit';
import { persistStore, persistReducer, FLUSH, REHYDRATE, PAUSE, PERSIST, PURGE, REGISTER } from 'redux-persist';
import storage from 'redux-persist/lib/storage';
import { combineReducers } from 'redux';
import profileReducer from './slices/profileSlice';
import eventsReducer from './slices/eventsSlice';
import ordersReducer from './slices/ordersSlice';
import authReducer from './slices/authSlice';
const rootReducer = combineReducers({
  auth: authReducer,
  profile: profileReducer,
  events: eventsReducer,
  orders: ordersReducer,
});

const persistConfig = {
  key: 'root',
  storage,
  whitelist: ['profile']
};

const persistedReducer = persistReducer(persistConfig, rootReducer);

export const store = configureStore({
  reducer: persistedReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: {
        ignoredActions: [FLUSH, REHYDRATE, PAUSE, PERSIST, PURGE, REGISTER],
      },
    }),
});

export const persistor = persistStore(store);


