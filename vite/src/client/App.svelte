<script lang="ts">
  import { QueryClient, QueryClientProvider } from "@tanstack/svelte-query";
  import Desktop from "./ui/Desktop.svelte";

  const queryClient = new QueryClient();
</script>

<QueryClientProvider client={queryClient}>
  <Desktop />
</QueryClientProvider>

<svg xmlns="http://www.w3.org/2000/svg" class="hidden">
  <filter id="blue-filter" color-interpolation-filters="sRGB">
    <feColorMatrix
      type="matrix"
      values=" 0.000  0.000  0.000  0.000  0.000 
               0.000  0.000  0.000  0.000  0.000 
               0.500  0.500  0.500  0.000  0.000 
               0.000  0.000  0.000  1.000  0.000"
    >
    </feColorMatrix>
  </filter>
</svg>
