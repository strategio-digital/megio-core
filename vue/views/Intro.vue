<template>
    <div style="display: flex; flex-flow: column; align-items: center; justify-content: space-between; height: 100%; width: 100%; padding: 3rem;">
        <div></div>
        <div style="text-align: center">
            <a href="https://strategio.digital" target="_blank">
                <img src="@/assets/strategio.svg" alt="Strategio SaaS" width="100" height="100" />
            </a>
            <h1 style="margin: 1rem 0">Strategio SaaS</h1>
            <p>Nejrychlejší způsob pro tvorbu webů a aplikací.</p>
        </div>
        <div style="display: flex; flex-flow: row; justify-content: space-between;width: 100%; opacity: .3">
            <div>
                <a href="//strategio.digital" target="_blank" style="color: white">Strategio Digital s.r.o.</a>
            </div>
            <div>
                <router-link :to="{ name: 'Login' }" style="color: white">Přihlásit se</router-link>
            </div>
        </div>
    </div>
</template>
