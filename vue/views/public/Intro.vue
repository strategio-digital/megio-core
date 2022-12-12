<template>
    <div class="d-flex flex-column justify-space-between w-100 h-100 pa-10 text-white bg-gradient-blue">
        <div></div>
        <div style="text-align: center">
            <img src="@/assets/img/strategio.svg" alt="Strategio SaaS" width="100" height="100" />
            <h1 class="mt-5">Strategio SaaS</h1>
            <p>Nástroj pro tvorbu byznysových webů a aplikací.</p>
        </div>
        <div class="d-flex flex-row justify-space-between w-100" style="opacity: .3">
            <div>
                <a href="//strategio.digital" target="_blank" class="text-white">Strategio Digital s.r.o.</a>
            </div>
            <div>
                <router-link :to="{ name: 'Login' }" class="text-white">Přihlásit se</router-link>
            </div>
        </div>
    </div>
</template>