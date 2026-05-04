import { defineConfig } from "vite";
import { resolve } from "path";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => ({
  plugins: [tailwindcss()],
  root: ".",
  publicDir: false,
  base: mode === "development" ? "/" : "/front/dist/", // <-- dev vs prod
  server: {
    port: 3000,
    strictPort: true,
    cors: true,
    hmr: { host: "localhost", port: 3000 },
  },
  resolve: {
    extensions: [".js", ".ts", ".jsx", ".tsx"],
    alias: { "@": resolve(__dirname, "resources/js/front") },
  },
  build: {
    outDir: "public/front/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: { main: resolve(__dirname, "resources/js/front/main.js") },
      output: {
        entryFileNames: "js/main.bundle.js",
        assetFileNames: (assetInfo) =>
          assetInfo.name.endsWith(".css")
            ? "css/main.css"
            : "assets/[name].[hash][extname]",
      },
    },
  },
}));
