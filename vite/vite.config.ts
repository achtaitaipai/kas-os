import { defineConfig } from "vite";
import liveReload from "vite-plugin-live-reload";
import { resolve } from "node:path";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    liveReload([
      __dirname + "/../src/**/*.php",
      __dirname + "/../src/**/*.twig",
    ]),
    svelte(),
    tailwindcss(),
  ],
  root: "src",
  base: process.env.APP_ENV === "development" ? "/" : "/dist/",

  build: {
    outDir: "../../public/dist",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: [
        resolve(__dirname, "src/admin.ts"),
        resolve(__dirname, "src/client.ts"),
      ],
      output: {
        manualChunks(id) {
          if (id.includes("node_modules")) {
            return "vendor";
          }
        },
      },
    },
  },

  server: {
    strictPort: true,
    port: 5133,
  },
});
