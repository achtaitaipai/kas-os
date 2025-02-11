<script lang="ts">
  import { activeWindow, WindowData } from "../../Windows.svelte";
  import { queryApi } from "../../lib/api";

  let { windowData }: { windowData: WindowData } = $props();
  const query = $derived(queryApi(windowData.path));
  function handleClick() {
    activeWindow(windowData.id);
    if (windowData.fold) {
      windowData.fold = false;
    }
  }
</script>

<button
  class="flex items-center cursor-pointer gap-2 px-2 py-1 hover:bg-neutral-950/10 hover:opacity-100 aria-[current=true]:bg-neutral-950/10"
  onclick={() => handleClick()}
  aria-current={windowData.active}
  class:opacity-60={windowData.fold}
>
  {#if $query.isLoading}
    <span
      class="whitespace-nowrap overflow-hidden text-ellipsis block break-all min-w-0 max-w-32"
    >
      Loading...
    </span>
  {:else if $query.isSuccess}
    <img
      src="/icons/{$query.data.type}.png"
      class="size-5 object-contain min-w-0 shrink-0"
      alt=""
    />
    <span
      class="whitespace-nowrap overflow-hidden text-ellipsis block break-all min-w-0 max-w-32"
    >
      {$query.data.name}
    </span>
  {/if}
</button>
