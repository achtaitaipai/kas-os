<script lang="ts">
  import Window from "../ui/Window/index.svelte";
  import { openWindow, getWindows } from "../Windows.svelte";
  import Icon from "../ui/Icon.svelte";
  import Bar from "../ui/Bar/index.svelte";
  import { onMount } from "svelte";
  import { queryApi } from "../lib/api";
  let query = queryApi("/");

  onMount(() => {
    const urlParam = new URLSearchParams(location.search);
    if (urlParam.has("path")) openWindow(urlParam.get("path")!);
  });
</script>

<div class="h-dvh w-screen grid grid-rows-[1fr_auto] dark:text-white | desktop">
  <main class="h-full min-h-0 relative">
    <div class="flex flex-col flex-wrap content-start gap-4 p-2 h-full">
      {#if $query.isSuccess}
        {#if $query.data.type === "directory"}
          {#each $query.data.childrens as file}
            <Icon fileData={file} onclick={() => openWindow(file.path)} />
          {/each}
        {/if}
      {/if}
    </div>
    {#each getWindows() as windowData}
      {#if !windowData.fold}
        {#key windowData.id}
          <Window {windowData} />
        {/key}
      {/if}
    {/each}
  </main>
  <Bar />
</div>

<style>
  .desktop {
    background-color: var(--_bg-color);
    background-image: var(--_bg-img);
    background-size: var(--_bg-size);
    background-repeat: var(--_bg-repeat);
    background-position: center center;
  }
</style>
