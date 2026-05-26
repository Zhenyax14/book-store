<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
</script>

<template>
  <div class="auth-shell">
    <header class="auth-header">
      <RouterLink to="/" class="logo">
        <span class="logo__book">Book</span><span class="logo__shop">shop</span>
      </RouterLink>
      <RouterLink to="/" class="auth-header__back">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
        Volver a la tienda
      </RouterLink>
    </header>

    <main class="auth-main">
      <aside class="auth-panel">
        <div class="auth-panel__backdrop" />
        <div class="auth-panel__content">
          <div class="auth-panel__quote">
            <div class="auth-panel__quote-mark">"</div>
            <blockquote>Un libro es un sueño que tienes en tus manos.</blockquote>
            <cite>— Neil Gaiman</cite>
          </div>

          <div class="auth-panel__books">
            <div class="book-stack">
              <div class="book-stack__book book-stack__book--1">
                <div class="book-stack__spine" />
                <div class="book-stack__face"><span>DUNE</span><small>Frank Herbert</small></div>
              </div>
              <div class="book-stack__book book-stack__book--2">
                <div class="book-stack__spine" />
                <div class="book-stack__face"><span>1984</span><small>G. Orwell</small></div>
              </div>
              <div class="book-stack__book book-stack__book--3">
                <div class="book-stack__spine" />
                <div class="book-stack__face"><span>STORM</span><small>G.R.R. Martin</small></div>
              </div>
            </div>
          </div>

          <div class="auth-panel__features">
            <div class="auth-panel__feature">
              <span class="auth-panel__feature-icon">📚</span>
              <span>+50.000 títulos digitales</span>
            </div>
            <div class="auth-panel__feature">
              <span class="auth-panel__feature-icon">⭐</span>
              <span>Suscripción desde 9,99€/mes</span>
            </div>
            <div class="auth-panel__feature">
              <span class="auth-panel__feature-icon">📱</span>
              <span>Lee en cualquier dispositivo</span>
            </div>
          </div>
        </div>
      </aside>

      <div class="auth-form-col">
        <div class="auth-form-wrap">
          <div class="auth-tabs">
            <RouterLink :to="{ name: 'login' }" class="auth-tab" active-class="active">
              Iniciar sesión
            </RouterLink>
            <RouterLink :to="{ name: 'register' }" class="auth-tab" active-class="active">
              Crear cuenta
            </RouterLink>
          </div>

          <RouterView />
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;500;600;700&display=swap');

.auth-shell {
  --amber: #e8a020;
  --amber-dk: #c88010;
  --dark: #1a1a1a;
  --text: #2c2c2c;
  --muted: #777;
  --light: #aaa;
  --border: #ded6c6;
  --bg: #f7f4ef;
  --white: #ffffff;
  --panel-bg: #1a0e04;

  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg);
  font-family: 'Barlow', sans-serif;
  color: var(--text);
  animation: shellFadeIn 0.3s ease both;
}
@keyframes shellFadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Header */
.auth-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 32px;
  background: var(--white);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  z-index: 10;
}

.logo {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.7rem;
  line-height: 1;
  text-decoration: none;
}
.logo__book {
  color: var(--amber);
  font-style: italic;
  font-weight: 700;
}
.logo__shop {
  color: var(--dark);
  font-weight: 400;
}

.auth-header__back {
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.2s;
}
.auth-header__back:hover {
  color: var(--amber);
}
.auth-header__back:hover svg {
  transform: translateX(-3px);
}
.auth-header__back svg {
  transition: transform 0.2s;
}

/* Layout */
.auth-main {
  flex: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 0;
}

/* Left panel */
.auth-panel {
  position: relative;
  background: var(--panel-bg);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.auth-panel__backdrop {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 80% 60% at 20% 80%, rgba(232, 160, 32, 0.18) 0%, transparent 70%),
    radial-gradient(ellipse 60% 80% at 80% 20%, rgba(139, 70, 20, 0.2) 0%, transparent 60%),
    linear-gradient(160deg, #2d1204 0%, #0f0602 100%);
}
.auth-panel::after {
  content: '';
  position: absolute;
  inset: 0;
  opacity: 0.03;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
  pointer-events: none;
}
.auth-panel__content {
  position: relative;
  z-index: 2;
  padding: 48px 52px;
  display: flex;
  flex-direction: column;
  gap: 52px;
  max-width: 460px;
}

/* Quote */
.auth-panel__quote {
  animation: panelIn 0.8s ease 0.1s both;
}
.auth-panel__quote-mark {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 5rem;
  line-height: 0.6;
  color: var(--amber);
  opacity: 0.6;
  margin-bottom: 8px;
}
.auth-panel__quote blockquote {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.5rem;
  font-style: italic;
  color: rgba(255, 255, 255, 0.88);
  line-height: 1.5;
  margin-bottom: 12px;
}
.auth-panel__quote cite {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--amber);
  opacity: 0.8;
  font-style: normal;
}

/* Book stack */
.auth-panel__books {
  animation: panelIn 0.8s ease 0.25s both;
  display: flex;
  justify-content: center;
}
.book-stack {
  display: flex;
  align-items: flex-end;
  height: 130px;
}
.book-stack__book {
  display: flex;
  position: relative;
  filter: drop-shadow(-8px 12px 24px rgba(0, 0, 0, 0.6));
  transition: transform 0.3s ease;
}
.book-stack__book:hover {
  transform: translateY(-8px) rotate(-1deg) !important;
}
.book-stack__spine {
  width: 18px;
  border-radius: 3px 0 0 3px;
}
.book-stack__face {
  width: 80px;
  height: 116px;
  border-radius: 0 4px 4px 0;
  padding: 12px 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  position: relative;
  overflow: hidden;
}
.book-stack__face::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}
.book-stack__face span {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 0.72rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.1;
  letter-spacing: 0.04em;
}
.book-stack__face small {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.55rem;
  font-weight: 500;
  letter-spacing: 0.1em;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
}
.book-stack__book--1 {
  transform: rotate(-4deg) translateX(10px);
  z-index: 1;
}
.book-stack__book--1 .book-stack__spine {
  background: linear-gradient(180deg, #7a4400, #4a2800);
}
.book-stack__book--1 .book-stack__face {
  background: linear-gradient(160deg, #c96812, #7a3a00);
}
.book-stack__book--2 {
  transform: rotate(2deg) translateX(-8px);
  z-index: 3;
}
.book-stack__book--2 .book-stack__spine {
  background: linear-gradient(180deg, #1a5a1a, #0d3a0d);
}
.book-stack__book--2 .book-stack__face {
  background: linear-gradient(160deg, #1a6a3a, #0d3d22);
}
.book-stack__book--3 {
  transform: rotate(-2deg) translateX(-4px);
  z-index: 2;
}
.book-stack__book--3 .book-stack__spine {
  background: linear-gradient(180deg, #1a1a4a, #0d0d2e);
}
.book-stack__book--3 .book-stack__face {
  background: linear-gradient(160deg, #3a1a8a, #1e0d5e);
}

/* Features */
.auth-panel__features {
  display: flex;
  flex-direction: column;
  gap: 12px;
  animation: panelIn 0.8s ease 0.4s both;
}
.auth-panel__feature {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.88rem;
  color: rgba(255, 255, 255, 0.6);
}
.auth-panel__feature-icon {
  font-size: 1.1rem;
  flex-shrink: 0;
}

@keyframes panelIn {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Right column */
.auth-form-col {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 32px;
  background: var(--bg);
  overflow-y: auto;
}
.auth-form-wrap {
  width: 100%;
  max-width: 420px;
  animation: formIn 0.5s ease both;
}
@keyframes formIn {
  from {
    opacity: 0;
    transform: translateX(16px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Tabs */
.auth-tabs {
  display: flex;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 4px;
  margin-bottom: 32px;
}
.auth-tab {
  flex: 1;
  background: none;
  border: none;
  padding: 10px 16px;
  border-radius: 7px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--muted);
  transition: all 0.25s ease;
  text-decoration: none;
  text-align: center;
  display: block;
}
.auth-tab.active {
  background: var(--amber);
  color: var(--white);
  box-shadow: 0 2px 12px rgba(232, 160, 32, 0.3);
}
.auth-tab:not(.active):hover {
  color: var(--text);
}

/* Responsive */
@media (max-width: 900px) {
  .auth-main {
    grid-template-columns: 1fr;
  }
  .auth-panel {
    display: none;
  }
  .auth-form-col {
    min-height: calc(100vh - 57px);
    padding: 32px 24px;
  }
}
@media (max-width: 480px) {
  .auth-header {
    padding: 14px 20px;
  }
  .auth-form-wrap {
    max-width: 100%;
  }
}
</style>
