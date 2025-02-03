<script lang="ts">
  import { fetchApi } from "../../api";
  import Handle from "./_Handle.svelte";
  import Resizer from "./_Resizer.svelte";
  import TextViewer from "./TextViewer.svelte";

  let { path = $bindable() }: { path: string } = $props();

  let left = $state(100);
  let top = $state(100);
  let width = $state(500);
  let height = $state(500);

  let isResizing = $state(false);
  let isMoving = $state(false);
  let dataPromise = $derived(fetchApi(path));

  async function back() {
    const data = await dataPromise;
    path = data.parent;
  }
</script>

<article
  class="absolute rounded-xs"
  style="left: {left}px; top: {top}px; width: {width}px; height: {height}px;"
  class:select-none={isResizing || isMoving}
>
  <Resizer
    bind:width
    bind:height
    bind:left
    bind:top
    bind:isResizing
    enabled={!isMoving}
  >
    <div class="flex flex-col h-full rounded-sm border-2 overflow-hidden">
      <Handle
        bind:left
        bind:top
        enabled={!isResizing}
        active={true}
        bind:isMoving
      >
        <div class="flex items-center">
          <div class="py-1 px-2 grow text-white">
            {#await dataPromise}
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
            {:then data}
              <h2>{data.name}</h2>
            {:catch}
              <p>Error</p>
            {/await}
          </div>
          <div class="p-1 flex gap-1">
            <button
              title="fold"
              aria-label="fole"
              class="bg-neutral-200 p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-neutral-300"
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
              title="Toggle full screen"
              aria-label="toggle full screen"
              class="bg-neutral-200 p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-neutral-300"
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
              title="close"
              aria-label="close"
              class="bg-red-500 text-white p-1 aspect-square cursor-pointer rounded-xs transition-colors hover:bg-red-600"
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
        <div class="bg-neutral-300 p-0.5 flex gap-1 h-11 items-center">
          <p
            class="bg-neutral-100 px-2 h-9 grow border-2 border-neutral-300 flex items-center"
          >
            {path}
          </p>
          <button
            aria-label="back"
            class="bg-neutral-300 p-2 cursor-pointer rounded-xs transition-colors hover:bg-neutral-200"
            onclick={() => back()}
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
      </Handle>
      <div class="bg-neutral-50 grow min-h-0 overflow-y-auto">
        {#await dataPromise then file}
          {#if file.type === "markdown"}
            <TextViewer {file} />
          {:else}
            <p>{file.name}</p>
          {/if}
        {:catch}
          <p>Oups une erreur est survenue</p>
        {/await}
      </div>
    </div>
  </Resizer>
</article>
