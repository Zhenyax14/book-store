import { useRoute } from 'vue-router'
import { computed } from 'vue'

export function useBreadcrumb() {
  const route = useRoute()

  return computed(() => {
    let bc = route.meta?.breadcrumb

    if (typeof bc === 'function') {
      bc = bc(route)
    }

    return Array.isArray(bc) ? bc : []
  })
}
