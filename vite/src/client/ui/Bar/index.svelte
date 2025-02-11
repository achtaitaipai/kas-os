<script lang="ts">
  import { onMount } from "svelte";
  import { getSortedWindows } from "../../Windows.svelte";
  import Tab from "./_Tab.svelte";

  let hours = $state("");
  let minutes = $state("");

  onMount(() => {
    update();
    let interval = setInterval(update, 1000);
    return () => {
      clearInterval(interval);
    };
  });

  function update() {
    const now = new Date();
    hours = ("0" + now.getHours().toString()).slice(-2);
    minutes = ("0" + now.getMinutes().toString()).slice(-2);
  }
</script>

<aside
  class="border-t border-neutral-950/10 grid grid-cols-[1fr_auto] w-screen bg-neutral-100/30"
>
  <div class="flex p-1 gap-1 overflow-auto items-center">
    {#each getSortedWindows() as windowData}
      <Tab {windowData} />
      <hr
        class="w-[1px] h-4 bg-neutral-950/30 border-0 mx-1 last-of-type:hidden"
      />
    {/each}
  </div>
  <div class="py-2 px-4">{hours}:{minutes}</div>
</aside>
