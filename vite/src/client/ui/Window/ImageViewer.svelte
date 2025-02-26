<script lang="ts">
  import { queryApi, type FileData, type ImageFile } from "../../lib/api";
  import { t } from "../../lib/lang";

  let { file, path = $bindable() }: { file: ImageFile; path: string } =
    $props();

  const query = $derived(queryApi(file.parent!));
  function getNeighbors(directory: FileData) {
    if (directory.type !== "directory") return [];
    return directory.childrens.filter((el) => el.type === "image");
  }
  async function nextImg(items: ImageFile[], dir: number) {
    const index = items.findIndex((el) => el.path === file.path);
    const newIndex =
      (((index + dir) % items.length) + items.length) % items.length;
    path = items[newIndex].path;
  }
</script>

<div class="w-full h-full min-h-0 flex flex-col">
  <div class="min-h-0 w-full h-full grow p-2">
    <img
      src="/static/{file.src}"
      alt={file.name}
      class="w-full h-full object-contain"
    />
  </div>
  {#if $query.isSuccess}
    {@const neighbors = getNeighbors($query.data)}
    {#if neighbors.length > 1}
      <div class="flex justify-center gap-2 py-1">
        <button
          onclick={() => nextImg(neighbors, -1)}
          aria-label={t("previous")}
          title={t("previous")}
          class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-700 cursor-pointer transition-colors rounded-xs"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            class="size-5 -scale-x-100 cursor-pointer"
            ><path
              fill="none"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M20.062 20.25V3.75M3.938 5.416V18.58a1.447 1.447 0 0 0 2.329 1.143l9.113-7.088a1.447 1.447 0 0 0-.087-2.344L6.18 4.216a1.447 1.447 0 0 0-2.242 1.2"
            /></svg
          >
        </button>
        <button
          onclick={() => nextImg(neighbors, 1)}
          aria-label={t("next")}
          title={t("next")}
          class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-700 cursor-pointer transition-colors rounded-xs"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            class="size-5"
            ><path
              fill="none"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M20.062 20.25V3.75M3.938 5.416V18.58a1.447 1.447 0 0 0 2.329 1.143l9.113-7.088a1.447 1.447 0 0 0-.087-2.344L6.18 4.216a1.447 1.447 0 0 0-2.242 1.2"
            /></svg
          >
        </button>
      </div>
    {/if}
  {/if}
</div>
