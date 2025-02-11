<script lang="ts">
	import type { Snippet } from 'svelte'
	const CURSORS = {
		topLeft: 'nwse-resize',
		bottomRight: 'nwse-resize',
		topRight: 'nesw-resize',
		bottomLeft: 'nesw-resize',
		top: 'ns-resize',
		bottom: 'ns-resize',
		left: 'ew-resize',
		right: 'ew-resize'
	}
	const MAXEDGEDISTANCE = 11

	type Edge = keyof typeof CURSORS | null

	let {
		width = $bindable(),
		height = $bindable(),
		left = $bindable(),
		top = $bindable(),
		isResizing = $bindable(false),
    enabled,
		children
	}: {
		width: number
		height: number
		left: number
		top: number
		isResizing: boolean
    enabled: boolean,
		children: Snippet
	} = $props()
	let element: HTMLElement | undefined = $state()
	let edge: Edge = $state(null)
	let oldPosition: [number, number] | undefined = $state()

	function handleClick(e: MouseEvent) {
		if (!edge || !enabled) return
		isResizing = true
		oldPosition = [e.clientX, e.clientY]
	}

	function handleUnclick() {
		isResizing = false
	}

	function handleMouseMove(e: MouseEvent) {
		if (!isResizing || !enabled) return
		if (oldPosition) {
			const [oldX, oldY] = oldPosition
			let [diffX, diffY] = [e.clientX - oldX, e.clientY - oldY]
			if (edge === 'left' || edge === 'topLeft' || edge === 'bottomLeft') {
				diffX *= -1
				left -= diffX
			}
			if (edge === 'top' || edge === 'topLeft' || edge === 'topRight') {
				diffY *= -1
				top -= diffY
			}
			if (edge !== 'top' && edge !== 'bottom') width += diffX
			if (edge !== 'left' && edge !== 'right') height += diffY
		}
		oldPosition = [e.clientX, e.clientY]
	}

	function setCursor(e: MouseEvent) {
		if (isResizing) return
		edge = getCurrentEdge(e.clientX, e.clientY)
	}

	function getCurrentEdge(x: number, y: number): Edge {
		if (!element) return null
		const distFromLeft = Math.abs(x - left)
		const distFromTop = Math.abs(y - top)
		const distFromRight = Math.abs(distFromLeft - width)
		const distFromBottom = Math.abs(distFromTop - height)
		const onLeft = distFromLeft < MAXEDGEDISTANCE
		const onTop = distFromTop < MAXEDGEDISTANCE
		const onRight = distFromRight < MAXEDGEDISTANCE
		const onBottom = distFromBottom < MAXEDGEDISTANCE

		if (onLeft && onTop) return 'topLeft'
		if (onTop && onRight) return 'topRight'
		if (onBottom && onLeft) return 'bottomLeft'
		if (onBottom && onRight) return 'bottomRight'
		if (onBottom) return 'bottom'
		if (onTop) return 'top'
		if (onLeft) return 'left'
		if (onRight) return 'right'
		return null
	}
</script>

<svelte:window onmousemove={handleMouseMove} onmouseup={handleUnclick} />
<!-- svelte-ignore a11y_no_static_element_interactions -->
<!-- svelte-ignore a11y_click_events_have_key_events -->
<div
	onmousedown={handleClick}
	onmousemove={setCursor}
	bind:this={element}
	style="cursor:{(edge&&enabled) ? CURSORS[edge] : 'auto'}"
  class="absolute inset-0 p-[6px]" 
>
	{@render children()}
</div>
