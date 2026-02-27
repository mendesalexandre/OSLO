import { computed } from "vue";
import { useRoute } from "vue-router";

export function useBreadcrumb() {
  const route = useRoute();

  const breadcrumbs = computed(() => {
    const matched = route.matched.filter((record) => record.meta?.breadcrumb);

    return matched.map((record, index) => ({
      label: record.meta.breadcrumb,
      to: index === matched.length - 1 ? undefined : { name: record.name },
      icon: record.meta.icon,
    }));
  });

  return {
    breadcrumbs,
  };
}
