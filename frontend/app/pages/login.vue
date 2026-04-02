<template>
  <div class="login-page">
    <div class="login-card">
      <div class="logo">
        <div class="logo-icon">C</div>
        <h1>{{ $t('app.name') }}</h1>
        <p class="tagline">{{ $t('app.tagline') }}</p>
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>

      <div class="form-section">
        <div v-if="!otpSent" class="phone-step">
          <label>{{ $t('auth.phone_label') }}</label>
          <div class="phone-input-wrapper">
            <span class="prefix">+972</span>
            <input
              v-model="phone"
              type="tel"
              maxlength="10"
              placeholder="50-000-0000"
              dir="ltr"
              @keyup.enter="handleSendOtp"
            />
          </div>
          <button class="btn-primary" :disabled="loading || !phone" @click="handleSendOtp">
            {{ loading ? $t('common.loading') : $t('auth.send_otp') }}
          </button>
        </div>

        <div v-else class="otp-step">
          <label>{{ $t('auth.enter_otp') }}</label>
          <div class="otp-inputs" dir="ltr">
            <input
              v-for="(_, i) in 6"
              :key="i"
              type="text"
              inputmode="numeric"
              maxlength="1"
              class="otp-digit"
              :value="otpDigits[i]"
              @input="onOtpInput(i, $event)"
              @keydown="onOtpKeydown(i, $event)"
            />
          </div>
          <button class="btn-primary" :disabled="loading || otpDigits.join('').length !== 6" @click="handleVerifyOtp">
            {{ loading ? $t('common.loading') : $t('auth.verify') }}
          </button>
          <button class="btn-text" @click="otpSent = false; error = ''">
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
    } else {
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
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.login-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.logo {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  font-weight: 700;
  color: white;
  margin-bottom: 1rem;
}

.logo h1 {
  font-size: 2rem;
  font-weight: 700;
  color: white;
  margin: 0;
}

.tagline {
  color: rgba(255, 255, 255, 0.6);
  margin: 0.5rem 0 0;
  font-size: 0.95rem;
}

.form-section label {
  display: block;
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 0.75rem;
}

.phone-input-wrapper {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1.25rem;
  transition: border-color 0.2s;
}

.phone-input-wrapper:focus-within {
  border-color: #3b82f6;
}

.phone-input-wrapper .prefix {
  padding: 0.85rem 1rem;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.95rem;
  border-inline-end: 1px solid rgba(255, 255, 255, 0.1);
  font-weight: 500;
}

.phone-input-wrapper input {
  flex: 1;
  padding: 0.85rem 1rem;
  background: transparent;
  border: none;
  color: white;
  font-size: 1.1rem;
  letter-spacing: 1px;
  outline: none;
}

.otp-inputs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.otp-digit {
  width: 48px;
  height: 56px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  color: white;
  font-size: 1.5rem;
  text-align: center;
  outline: none;
  transition: border-color 0.2s;
}

.otp-digit:focus {
  border-color: #3b82f6;
}

.btn-primary {
  width: 100%;
  padding: 0.85rem;
  background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.1s;
}

.btn-primary:hover {
  opacity: 0.9;
}

.btn-primary:active {
  transform: scale(0.98);
}

.btn-text {
  width: 100%;
  padding: 0.75rem;
  background: transparent;
  color: rgba(255, 255, 255, 0.6);
  border: none;
  font-size: 0.9rem;
  cursor: pointer;
  margin-top: 0.5rem;
}

.btn-text:hover {
  color: white;
}

.error-message {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-size: 0.85rem;
  margin-bottom: 1.25rem;
  text-align: center;
}
</style>
