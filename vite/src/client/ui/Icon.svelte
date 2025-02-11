<script lang="ts">
    import type { Directory } from '../lib/api';

  let {fileData, onclick}: {fileData:Directory["childrens"][number], onclick:()=>void} = $props();

  const extensions = {
    link: ".url",
    image: ".jpg",
    audio: ".mp3",
    markdown: ".txt",
    directory: "",
    unknown: ""
  } as const;

    let fullName = $derived(fileData.name + (extensions[fileData.type] ?? ""))


  let displayName = $derived.by(() => {
      const MAXLENGTH = 15
    if (fullName.length < MAXLENGTH) return fullName;
    return fullName.slice(0, MAXLENGTH - 3) + "...";
  });
    function handleClick(e:MouseEvent){
      e.stopPropagation()
      onclick()
    }


</script>

{#snippet content()}
  <img
    src="/icons/{fileData.type}.png"
    alt=""
    class="w-18 h-18 object-contain mx-auto group-hover:[filter:url(#blue-filter)]"
  />
  <h3 class="text-sm">
    <span class="group-hover:text-white group-hover:bg-blue-700 px-1.5 py-0.5">
      {displayName}
    </span>
  </h3>
{/snippet}

{#if fileData.type==="link"}
  <a href={fileData.url}
      target="_blank"
    class="grid gap-1.5 w-32 px-1 cursor-pointer group justify-items-center"
    title={fullName}
  >
    {@render content()}
  </a>
{:else}
  <button
    class="grid gap-1.5 w-32 px-1 cursor-pointer group justify-items-center"
    title={fullName}
    onclick={handleClick}
  >
    {@render content()}
  </button>
{/if}

