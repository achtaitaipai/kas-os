<script lang="ts">
  import { t } from "../../lib/lang";
  import { queryApi } from "../../lib/api";
  import Handle from "./_Handle.svelte";
  import Resizer from "./_Resizer.svelte";
  import ImageViewer from "./ImageViewer.svelte";
  import TextViewer from "./TextViewer.svelte";
  import AudioViewer from "./AudioViewer.svelte";
  import FileExplorer from "./FileExplorer.svelte";
  import { openWindow, WindowData } from "../../Windows.svelte";
  import { closeWindow, activeWindow } from "../../Windows.svelte";
  import { onMount } from "svelte";

  let { windowData }: { windowData: WindowData } = $props();

  let isResizing = $state(false);
  let isMoving = $state(false);
  const query = $derived(queryApi(windowData.path));
  async function back(parent: string) {
    if (!parent) return;
    windowData.path = parent;
  }

  function close() {
    closeWindow(windowData.id);
  }

  function fold(e: MouseEvent) {
    windowData.active = false;
    windowData.fold = true;
    e.stopPropagation();
  }

  function toggleFullscreen() {
    windowData.fullscreen = !windowData.fullscreen;
  }

  onMount(() => {
    const urlParam = new URLSearchParams(location.search);
    if (urlParam.has("path")) openWindow(urlParam.get("path")!);
  });
</script>

<!-- svelte-ignore a11y_click_events_have_key_events -->
<!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
<article
  class="absolute"
  style="left: {windowData.left}px; top: {windowData.top}px; width: {windowData.width}px; height: {windowData.height}px;"
  class:select-none={isResizing || isMoving}
  class:fullscreen={windowData.fullscreen}
  onmousedown={() => activeWindow(windowData.id)}
>
  <Resizer
    bind:width={windowData.width}
    bind:height={windowData.height}
    bind:left={windowData.left}
    bind:top={windowData.top}
    bind:isResizing
    enabled={!isMoving && !windowData.fullscreen && windowData.active}
  >
    <div
      class="flex flex-col h-full rounded-xs bg-gray-50 overflow-hidden shadow-sm"
    >
      <header>
        <Handle
          bind:left={windowData.left}
          bind:top={windowData.top}
          enabled={!isResizing && !windowData.fullscreen && windowData.active}
          bind:isMoving
        >
          <div
            class="grid grid-cols-[1fr_auto] bg-blue-700 items-center"
            class:bg-neutral-500={!windowData.active}
          >
            <div class="font-bold text-white py-1 px-2 grow shrink max-w-full">
              {#if $query.isLoading}
                <p class="flex gap-2 items-center">
                  <svg
                    class="size-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    ><circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    ></circle><path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path></svg
                  >
                  En chargement...
                </p>
              {:else if $query.isSuccess}
                <h2 class="break-all line-clamp-1 max-w-full">
                  {$query.data.name}
                </h2>
              {:else if $query.isError}
                <p>Error</p>
              {/if}
            </div>
            <div class="p-1.5 flex gap-1 shrink-0">
              <button
                title={t("minimize")}
                aria-label={t("minimize")}
                class="bg-neutral-200 p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-neutral-300"
                onclick={fold}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18 18 18"
                  />
                </svg>
              </button>
              <button
                title={t("fullscreen")}
                aria-label={t("fullscreen")}
                class="bg-neutral-200 p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-neutral-300"
                onclick={toggleFullscreen}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 5 19 5 19 19 5 19 z"
                  />
                </svg>
              </button>
              <button
                title={t("close")}
                aria-label={t("close")}
                class="bg-red-500 text-white p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-red-600"
                onclick={close}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18 18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </Handle>
        {#if $query.isSuccess}
          {#if $query.data.type === "directory"}
            <div class="bg-gray-200 p-1.5 flex gap-1.5 items-center">
              <div class="bg-white px-2 h-9 grow flex items-center">
                <p class=" break-all line-clamp-1">
                  {windowData.path}
                </p>
              </div>
              <button
                aria-label={t("back")}
                title={t("back")}
                class="p-2 not-disabled:cursor-pointer rounded-xs transition-colors bg-neutral-200/70 not-disabled:hover:bg-neutral-300/70 disabled:text-neutral-400"
                disabled={$query.data.parent === null ||
                  $query.data.parent === "/"}
                onclick={() => $query.data.parent && back($query.data.parent)}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M11.99 7.5 8.24 3.75m0 0L4.49 7.5m3.75-3.75v16.499h11.25"
                  />
                </svg>
              </button>
            </div>
          {/if}
        {/if}
      </header>
      <div class="grow min-h-0 overflow-y-auto">
        {#if $query.isSuccess}
          {#if $query.data.type === "markdown"}
            <TextViewer file={$query.data} />
          {:else if $query.data.type === "image"}
            <ImageViewer file={$query.data} bind:path={windowData.path} />
          {:else if $query.data.type === "audio"}
            <AudioViewer file={$query.data} />
          {:else if $query.data.type === "directory"}
            <FileExplorer file={$query.data} bind:path={windowData.path} />
          {:else}
            <p>{$query.data.name}</p>
          {/if}
        {:else if $query.isError}
          <p>Oups une erreur est survenue</p>
        {/if}
      </div>
    </div>
  </Resizer>
</article>

<style>
  .fullscreen {
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    height: 100% !important;
  }
  .fullscreen > :global(div) {
    padding: 0;
  }
  .fullscreen > :global(div > div) {
    border-radius: 0;
    box-shadow: none;
  }
</style>
