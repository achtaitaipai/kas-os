<script lang="ts">
  import type { Directory } from "../../lib/api";
  import { openWindow } from "../../Windows.svelte";
  import Icon from "../Icon.svelte";

  let { file, path = $bindable() }: { file: Directory; path: string } =
    $props();
  function click(newPath: string, type: string) {
    if (type === "directory") path = newPath;
    else openWindow(newPath);
  }
</script>

<div class="w-full p-4 flex flex-wrap gap-2">
  {#each file.childrens as item}
    <Icon fileData={item} onclick={() => click(item.path, item.type)} />
  {/each}
</div>
