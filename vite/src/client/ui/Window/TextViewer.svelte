<script lang="ts">
  import type { MarkdownFile } from "../../api";

  let { file }: { file: MarkdownFile } = $props();
</script>

<div class="prose mx-auto p-4">
  {@html file.content}
</div>
