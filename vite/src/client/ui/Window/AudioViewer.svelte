<script lang="ts">
  import { onMount } from "svelte";
  import type { AudioFile } from "../../lib/api";
  import { t } from "../../lib/lang";

  let { file }: { file: AudioFile } = $props();
  let audioEl: HTMLAudioElement | undefined = $state();
  let path = $state("");
  let playing = $state(false);
  let volume = $state(1);
  let progress = $state(0);

  const audioCtx = new AudioContext();
  const analyser = audioCtx.createAnalyser();
  analyser.fftSize = 256;
  const bufferLength = analyser.frequencyBinCount;
  const dataArray = new Uint8Array(bufferLength);

  function handlePlay() {
    playing = true;
    audioCtx.resume();
  }

  function handlePause() {
    playing = false;
  }

  function ram() {
    requestAnimationFrame(ram);
    if (!audioEl) return;
    analyser.getByteTimeDomainData(dataArray);
    const width = 300;
    const height = 100;
    let newPath = "M";

    const sliceWidth = width / bufferLength;

    for (let i = 0; i < bufferLength; i++) {
      const v = dataArray[i] / 128.0;
      const y = (v * height) / 2;
      const x = i === bufferLength - 1 ? width : i * sliceWidth;
      newPath += `${x} ${y} `;
    }
    path = newPath;
    progress = audioEl.currentTime / audioEl.duration;
  }

  onMount(() => {
    if (!audioEl) return;
    audioEl.addEventListener("play", handlePlay);
    audioEl.addEventListener("pause", handlePause);
    const source = audioCtx.createMediaElementSource(audioEl);
    source.connect(analyser);
    analyser.connect(audioCtx.destination);
    requestAnimationFrame(ram);
    return () => {
      audioEl?.removeEventListener("play", handlePlay);
      audioEl?.removeEventListener("pause", handlePause);
    };
  });

  function playPause() {
    if (playing) audioEl?.pause();
    else audioEl?.play();
  }

  function stop() {
    if (!audioEl) return;
    audioEl.pause();
    audioEl.currentTime = 0;
  }

  function muteUnmute() {
    volume = volume === 0 ? 1 : 0;
  }

  $effect(() => {
    if (audioEl) audioEl.volume = volume;
  });
</script>

<div class="w-full h-full min-h-0 grid grid-rows-[1fr_auto]">
  <audio src="static/{file.src}" bind:this={audioEl}></audio>
  <div class="w-full h-full min-h-0">
    <svg
      viewBox="0 0 300 100"
      xmlns="http://www.w3.org/2000/svg"
      class="w-full h-full object-contain bg-black fill-none stroke-white"
    >
      <path d={path} />
    </svg>
  </div>
  <div class="flex p-1 gap-2 items-center">
    <div class="flex items-center gap-1">
      <button
        aria-label={playing ? "pause" : "play"}
        title={t(playing ? "pause" : "play")}
        onclick={playPause}
        class="p-1 rounded-xs cursor-pointer hover:bg-neutral-200"
      >
        {#if !playing}
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"
            />
          </svg>
        {:else}
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15.75 5.25v13.5m-7.5-13.5v13.5"
            />
          </svg>
        {/if}
      </button>
      <button
        title={t("stop")}
        aria-label={t("stop")}
        onclick={stop}
        class="p-1 cursor-pointer rounded-xs hover:bg-neutral-200"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"
          />
        </svg>
      </button>
    </div>
    <div
      class="flex w-full h-full bg-gray-200 overflow-hidden"
      role="progressbar"
      aria-valuenow={progress}
      aria-valuemin="0"
      aria-valuemax="1"
    >
      <div
        class=" overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"
        style="width: {progress * 100}%"
      ></div>
    </div>
    <div class="relative group">
      <button
        title={t(volume === 0 ? "unmute" : "mute")}
        aria-label={t(volume === 0 ? "unmute" : "mute")}
        onclick={muteUnmute}
        class="p-1 cursor-pointer rounded-xs"
      >
        {#if volume > 0}
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z"
            />
          </svg>
        {:else}
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M17.25 9.75 19.5 12m0 0 2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6 4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z"
            />
          </svg>
        {/if}
      </button>
      <input
        type="range"
        min="0"
        max="1"
        bind:value={volume}
        step="0.1"
        class="[writing-mode:vertical-lr] [direction:rtl] absolute bottom-full left-1/2 cursor-pointer -translate-x-1/2 hidden group-hover:block"
      />
    </div>
  </div>
</div>
