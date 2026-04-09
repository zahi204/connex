<template>
  <div class="portal-page">
    <h1>My Rating</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="rating">
      <div class="rating-overview">
        <div class="stat-card large">
          <div class="stat-label">Overall Rating</div>
          <div class="stat-value">{{ rating.overall ?? 'N/A' }}</div>
        </div>
      </div>

      <div v-if="rating.categories && rating.categories.length" class="categories-section">
        <h2>Rating Breakdown</h2>
        <div class="categories-grid">
          <div v-for="c in rating.categories" :key="c.name" class="category-card">
            <div class="category-name">{{ c.name }}</div>
            <div class="category-score">{{ c.score }}</div>
          </div>
        </div>
      </div>

      <div v-if="rating.reviews && rating.reviews.length" class="reviews-section">
        <h2>Recent Reviews</h2>
        <div v-for="r in rating.reviews" :key="r.id" class="review-card">
          <div class="review-score">{{ r.score }}</div>
          <div class="review-content">
            <div v-if="r.project_name" class="review-project">{{ r.project_name }}</div>
            <div v-if="r.comment" class="review-comment">{{ r.comment }}</div>
            <div class="review-date">{{ r.created_at }}</div>
          </div>
        </div>
      </div>

      <p class="hint">Ratings are assigned by Connex coordinators and are read-only.</p>
    </div>
    <div v-else class="empty">No rating data available.</div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'subcontractor', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const rating = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/subcontractor/rating') as any
    rating.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load rating'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.empty { color: var(--cx-text-muted); text-align: center; padding: 2rem; }
.rating-overview { margin-bottom: 2rem; }
.stat-card { background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 12px; padding: 1.5rem; text-align: center; }
.stat-card.large .stat-value { font-size: 3rem; }
.stat-label { color: var(--cx-text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; }
.stat-value { color: var(--cx-text-primary); font-weight: 700; }
.categories-section, .reviews-section { margin-bottom: 1.5rem; }
.categories-section h2, .reviews-section h2 { color: var(--cx-text-primary); font-size: 1.1rem; margin-bottom: 0.75rem; }
.categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; }
.category-card { background: var(--cx-bg-card); border-radius: 8px; padding: 0.75rem; display: flex; align-items: center; justify-content: space-between; }
.category-name { color: var(--cx-text-secondary); font-size: 0.85rem; }
.category-score { color: var(--cx-text-primary); font-weight: 700; font-size: 1.1rem; }
.review-card { background: var(--cx-bg-card); border-radius: 8px; padding: 0.75rem 1rem; display: flex; gap: 1rem; margin-bottom: 0.5rem; }
.review-score { color: var(--cx-text-primary); font-weight: 700; font-size: 1.2rem; min-width: 2.5rem; text-align: center; }
.review-project { color: var(--cx-text-primary); font-weight: 500; margin-bottom: 0.2rem; }
.review-comment { color: var(--cx-text-muted); font-size: 0.85rem; }
.review-date { color: var(--cx-text-muted); font-size: 0.75rem; margin-top: 0.2rem; }
.hint { color: var(--cx-text-muted); font-size: 0.8rem; font-style: italic; margin-top: 1rem; }
</style>
