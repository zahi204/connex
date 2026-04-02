import type { ComputedRef, MaybeRef } from 'vue'

type ComponentProps<T> = T extends new(...args: any) => { $props: infer P } ? NonNullable<P>
  : T extends (props: infer P, ...args: any) => any ? P
  : {}

declare module 'nuxt/app' {
  interface NuxtLayouts {
    agency: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/agency.vue").default>
    blank: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/blank.vue").default>
    default: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/default.vue").default>
    developer: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/developer.vue").default>
    subcontractor: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/subcontractor.vue").default>
    worker: ComponentProps<typeof import("/Users/zahiissa/Sites/connex/frontend/app/layouts/worker.vue").default>
  }
  export type LayoutKey = keyof NuxtLayouts extends never ? string : keyof NuxtLayouts
  interface PageMeta {
    layout?: MaybeRef<LayoutKey | false> | ComputedRef<LayoutKey | false> | {
      [K in LayoutKey]: {
        name?: MaybeRef<K | false> | ComputedRef<K | false>
        props?: NuxtLayouts[K]
      }
    }[LayoutKey]
  }
}