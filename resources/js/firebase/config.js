/**
 * Firebase Configuration
 * This file contains Firebase initialization and configuration
 */

import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyB-h_XCrdqpO38AFP2ZvUdXd0Yq4MB104w",
    authDomain: "sels-e407c.firebaseapp.com",
    projectId: "sels-e407c",
    storageBucket: "sels-e407c.firebasestorage.app",
    messagingSenderId: "689946728760",
    appId: "1:689946728760:web:605322353c7706bb8409cd",
    measurementId: "G-SZF8EVP8Y1"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Analytics (only in browser environment)
let analytics = null;
if (typeof window !== 'undefined') {
    analytics = getAnalytics(app);
}

// Initialize Messaging (only in browser environment and if service worker is supported)
let messaging = null;
if (typeof window !== 'undefined' && 'serviceWorker' in navigator) {
    try {
        messaging = getMessaging(app);
    } catch (error) {
        console.warn('Firebase Messaging initialization failed:', error);
    }
}

export { app, analytics, messaging, firebaseConfig };
