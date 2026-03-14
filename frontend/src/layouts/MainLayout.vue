<script setup>
import Sidebar from "../components/Sidebar.vue"
import Navbar from "../components/Navbar.vue"
import Toast from "../components/Toast.vue"
</script>

<template>
    <div class="flex">

        <Sidebar />

        <div class="flex-1">

            <Navbar />

            <main class="p-6 bg-slate-100 min-h-screen">
                <router-view />
            </main>

        </div>

        <Toast />

    </div>
</template>