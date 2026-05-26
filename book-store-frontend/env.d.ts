/// <reference types="vite/client" />
import 'vue-router'
import { BreadcrumbItem } from '@/types'
import type { RouteLocationNormalizedLoaded } from 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    breadcrumb?: BreadcrumbItem[] | ((route: RouteLocationNormalizedLoaded) => BreadcrumbItem[])
  }
}
