<template>
  <div class="login-page">
    <div class="login-card">
      <div class="brand">
        <div class="brand-icon">C</div>
        <h1>{{ $t('app.name') }}</h1>
        <p class="cx-text-muted">{{ $t('app.tagline') }}</p>
      </div>

      <div v-if="error" class="cx-error" style="margin-bottom: 1.25rem; text-align: center;">{{ error }}</div>

      <div class="form-section">
        <div v-if="!otpSent" class="phone-step">
          <label class="cx-label">{{ $t('auth.phone_label') }}</label>
          <div class="phone-input-wrapper">
            <span class="prefix cx-mono">+972</span>
            <input
              v-model="phone"
              type="tel"
              maxlength="10"
              placeholder="50-000-0000"
              dir="ltr"
              class="cx-mono"
              @keyup.enter="handleSendOtp"
            />
          </div>
          <button class="cx-btn cx-btn-primary btn-full" :disabled="loading || !phone" @click="handleSendOtp">
            {{ loading ? $t('common.loading') : $t('auth.send_otp') }}
          </button>
        </div>

        <div v-else class="otp-step">
          <label class="cx-label">{{ $t('auth.enter_otp') }}</label>
          <div class="otp-inputs" dir="ltr">
            <input
              v-for="(_, i) in 6"
              :key="i"
              type="text"
              inputmode="numeric"
              maxlength="1"
              class="otp-digit cx-mono"
              :value="otpDigits[i]"
              @input="onOtpInput(i, $event)"
              @keydown="onOtpKeydown(i, $event)"
            />
          </div>
          <button class="cx-btn cx-btn-primary btn-full" :disabled="loading || otpDigits.join('').length !== 6" @click="handleVerifyOtp">
            {{ loading ? $t('common.loading') : $t('auth.verify') }}
          </button>
          <button class="btn-back" @click="otpSent = false; error = ''">
            {{ $t('common.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'blank',
})

const { sendOtp, verifyOtp } = useAuth()
const authStore = useAuthStore()

const phone = ref('')
const otpSent = ref(false)
const otpDigits = ref<string[]>(['', '', '', '', '', ''])
const loading = ref(false)
const error = ref('')

const fullPhone = computed(() => `+972${phone.value.replace(/\D/g, '')}`)

const handleSendOtp = async () => {
  error.value = ''
  loading.value = true
  try {
    await sendOtp(fullPhone.value)
    otpSent.value = true
  } catch (e: any) {
    error.value = e?.data?.message || e?.message || 'Failed to send OTP'
  } finally {
    loading.value = false
  }
}

const handleVerifyOtp = async () => {
  const code = otpDigits.value.join('')
  if (code.length !== 6) return

  error.value = ''
  loading.value = true
  try {
    const result = await verifyOtp(fullPhone.value, code)
    if (result.needs_role_selection) {
      await navigateTo('/select-role')
    }
    else if (result.needs_onboarding && authStore.user?.role) {
      await navigateTo(`/wizard/${authStore.user.role}`)
    }
    else {
      const role = authStore.user?.role
      await navigateTo(`/${role}`)
    }
  } catch (e: any) {
    error.value = e?.data?.message || e?.message || 'Invalid OTP'
  } finally {
    loading.value = false
  }
}

const onOtpInput = (index: number, event: Event) => {
  const target = event.target as HTMLInputElement
  const val = target.value.replace(/\D/g, '')
  otpDigits.value[index] = val.slice(-1)
  target.value = otpDigits.value[index]

  if (val && index < 5) {
    const next = target.parentElement?.children[index + 1] as HTMLInputElement
    next?.focus()
  }
}

const onOtpKeydown = (index: number, event: KeyboardEvent) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    const prev = (event.target as HTMLElement).parentElement?.children[index - 1] as HTMLInputElement
    prev?.focus()
  }
}
</script>

<style scoped>
.login-page {
  min-height: min(100vh, 100dvh);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem 1.25rem 2rem;
  background: transparent;
}

.login-card {
  position: relative;
  background: rgba(26, 34, 54, 0.7);
  backdrop-filter: blur(24px) saturate(160%);
  -webkit-backdrop-filter: blur(24px) saturate(160%);
  border: 1px solid var(--cx-border);
  border-radius: var(--cx-radius-xl);
  box-shadow: var(--cx-shadow-elevated), var(--cx-shadow-glow);
  padding: 2.75rem 2.25rem 2.5rem;
  width: 100%;
  max-width: 420px;
  overflow: hidden;
}

.login-card::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  padding: 1px;
  background: linear-gradient(135deg, rgba(56, 189, 248, 0.4), transparent 40%, transparent 60%, rgba(129, 140, 248, 0.3));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  pointer-events: none;
}

.brand {
  text-align: center;
  margin-bottom: 2rem;
  position: relative;
}

.brand-icon {
  width: 60px;
  height: 60px;
  background: var(--cx-gradient-accent);
  border-radius: var(--cx-radius-md);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-family: var(--cx-font-display);
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--cx-text-inverse);
  margin-bottom: 1.1rem;
  box-shadow: 0 0 30px rgba(56, 189, 248, 0.45);
}

.brand h1 {
  font-family: var(--cx-font-display);
  font-size: var(--cx-font-xl);
  font-weight: 800;
  background: var(--cx-gradient-brand);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 0.35rem;
  letter-spacing: -0.02em;
}

.phone-input-wrapper {
  display: flex;
  align-items: center;
  background: var(--cx-bg-input);
  border: 1px solid var(--cx-border);
  border-radius: var(--cx-radius-md);
  overflow: hidden;
  margin-bottom: 1rem;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.phone-input-wrapper:focus-within {
  border-color: var(--cx-primary);
  box-shadow: 0 0 0 3px var(--cx-primary-ring);
}

.phone-input-wrapper .prefix {
  padding: 0.75rem 0.875rem;
  color: var(--cx-text-muted);
  font-size: var(--cx-font-sm);
  border-inline-end: 1px solid var(--cx-border);
  font-weight: 500;
  background: var(--cx-bg-muted);
}

.phone-input-wrapper input {
  flex: 1;
  padding: 0.75rem 0.875rem;
  background: transparent;
  border: none;
  color: var(--cx-text-primary);
  font-size: 1rem;
  letter-spacing: 0.5px;
  outline: none;
  min-width: 0;
}

.otp-inputs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1.25rem;
}

.otp-digit {
  width: 44px;
  height: 52px;
  background: var(--cx-bg-input);
  border: 1px solid var(--cx-border);
  border-radius: var(--cx-radius-md);
  color: var(--cx-text-primary);
  font-size: 1.25rem;
  font-weight: 600;
  text-align: center;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.otp-digit:focus {
  border-color: var(--cx-primary);
  box-shadow: 0 0 0 3px var(--cx-primary-ring);
}

.btn-full {
  width: 100%;
}

.btn-back {
  width: 100%;
  min-height: var(--cx-tap-min);
  padding: 0.5rem;
  background: transparent;
  color: var(--cx-text-muted);
  border: none;
  font-size: var(--cx-font-sm);
  font-weight: 500;
  cursor: pointer;
  margin-top: 0.5rem;
}

.btn-back:hover {
  color: var(--cx-text-primary);
}
</style>
