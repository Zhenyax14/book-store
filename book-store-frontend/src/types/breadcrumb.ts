import type { RouteLocationNormalizedLoaded } from 'vue-router'

type BreadcrumbItem = { text: string; to?: string }
type BreadcrumbFn = (route: RouteLocationNormalizedLoaded) => BreadcrumbItem[]
type Breadcrumb = BreadcrumbItem[] | BreadcrumbFn
