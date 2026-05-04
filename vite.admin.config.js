import { defineConfig } from "vite";
import { resolve } from "path";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => ({
  plugins: [tailwindcss()],
  root: ".",
  publicDir: false,
  base: mode === "development" ? "/" : "/admin/dist/", // <-- dev vs prod
  server: {
    port: 3000,
    strictPort: true,
    cors: true,
    hmr: { host: "localhost", port: 3000 },
  },
  resolve: {
    extensions: [".js", ".ts", ".jsx", ".tsx"],
    alias: { "@": resolve(__dirname, "resources/js/admin") },
  },
  build: {
    outDir: "public/admin/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, "resources/js/admin/main.js"),
        login: resolve(__dirname, "resources/js/admin/login.js"),
      },
      output: {
        entryFileNames: "js/[name].bundle.js",
        chunkFileNames: "js/[name].[hash].js",
        assetFileNames: (assetInfo) =>
          assetInfo.name && assetInfo.name.endsWith(".css")
            ? "css/[name][extname]"
            : "assets/[name].[hash][extname]",
      },
    },
  },
}));
