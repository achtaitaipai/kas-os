<script lang="ts">
  import type { MarkdownFile } from "../../lib/api";

  let { file }: { file: MarkdownFile } = $props();
</script>

<div class="bg-neutral-200 min-h-full h-full">
  <div class="prose mx-auto py-4 px-8 bg-white min-h-full h-full overflow-auto">
    {@html file.content}
  </div>
</div>
