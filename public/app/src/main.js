import { createApp } from 'vue'
import App from './App.vue'

const el = document.getElementById(import.meta.env.VITE_APP_ID)

createApp(App).mount(el)
