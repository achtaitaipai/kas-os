<script lang="ts">
	import type { Snippet } from 'svelte'

	let {
		left = $bindable(),
		top = $bindable(),
		isMoving = $bindable(false),
    enabled,
		children
	}: {
		left: number
		top: number
		isMoving: boolean
    enabled: boolean
		children: Snippet
	} = $props()

	let oldPosition: [number, number] | undefined = $state()

	function handleClick(e: MouseEvent) {
		if (!enabled) return
		oldPosition = [e.clientX, e.clientY]
		isMoving = true
	}
	function handleUnclick() {
		isMoving = false
	}
	function handleMouseMove(e: MouseEvent) {
		if (!enabled || !isMoving) return
		if (oldPosition) {
			const [oldX, oldY] = oldPosition
			let [diffX, diffY] = [e.clientX - oldX, e.clientY - oldY]
			left += diffX
			top += diffY
		}
		oldPosition = [e.clientX, e.clientY]
	}
</script>

<svelte:window onpointermove={handleMouseMove} onpointerup={handleUnclick} />

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div class="" onpointerdown={handleClick}>
	{@render children()}
</div>

